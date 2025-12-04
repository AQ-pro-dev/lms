<?php

namespace App\Livewire;

use App\Models\Booking;
use App\Models\CalendarEvent;
use App\Models\Events;
use App\Models\Notification;
use App\Services\MicrosoftGraphService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Livewire\Component;
use Stripe\Stripe;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class EventCalendar extends Component
{

    protected $microsoftGraphService;

    public function __construct()
    {
        $this->microsoftGraphService = app(MicrosoftGraphService::class);
    }

    use LivewireAlert;

    public $modalType;
    public $events = [];
    public $classes = [];
    public $selectedEvent;
    public $eventCards;
    public $stripeKey;
    public $selectedEventId;
    public $isPaid;
    public $price;
    public $slots;

    public function mount()
    {
        $this->stripeKey = env('STRIPE_KEY');

        $user = Auth::user();

        $this->eventCards = Events::with('users')->get();

        if ($user) {
            $userTimezone = $user->timezone;
            $events = CalendarEvent::with(['event', 'class', 'user'])
                ->where('user_id', $user->id)
                ->orWhere(function ($query) use ($user) {
                    $query->whereNotNull('event_id')
                        ->whereHas('event', function ($subQuery) use ($user) {
                            $subQuery->where('user_id', $user->id);
                        });
                })
                ->get()
                ->map(function ($calendarEvent) use ($userTimezone) {
                    $isEvent = !is_null($calendarEvent->event_id);
                    $source = $isEvent ? $calendarEvent->event : $calendarEvent->class;


                    return [
                        'id' => $calendarEvent->id,
                        'title' => $source->title,
                        'date' => Carbon::parse($source->class_date, 'UTC')->setTimezone($userTimezone)->toIso8601String(),
                        'start_date' => Carbon::parse($source->booking_start_date ?? $source->start_date, 'UTC')->setTimezone($userTimezone)->toIso8601String(),
                        'end_date' => Carbon::parse($source->booking_end_date ?? $source->end_date, 'UTC')->setTimezone($userTimezone)->toIso8601String(),
                        'description' => $source->description,
                        'spotsLeft' => $source->capacity,
                        'class_time' => Carbon::parse($source->class_time, 'UTC')->setTimezone($userTimezone)->toTimeString(),
                        'onsite' => $source->onsite_address,
                        'online' => $source->teams_link,
                        'type' => $isEvent ? 'event' : 'class',
                        'created_by' => optional($calendarEvent->user)->first_name,
                    ];
                });

            $this->events = $events;
        } else {
            $this->events = [];  // No user-specific events for guest users
        }

        // Dispatch data to frontend
        $this->dispatch('eventsLoaded', $this->events);
        $this->dispatch('eventCardsLoaded', $this->eventCards);
    }

    public function selectEvent($event, $type)
    {
        $this->selectedEvent = null;

        $user = Auth::user();
        if ($user) {
            $userTimezone = $user->timezone;
            $event['date'] = Carbon::parse($event['class_date'], 'UTC')->setTimezone($userTimezone)->toIso8601String();
            $event['class_time'] = Carbon::parse($event['class_time'], 'UTC')->setTimezone($userTimezone)->toTimeString();
            $event['booking_start_date'] = Carbon::parse($event['booking_start_date'], 'UTC')->setTimezone($userTimezone)->toDateString();
            $event['booking_end_date'] = Carbon::parse($event['booking_end_date'], 'UTC')->setTimezone($userTimezone)->toDateString();
            $event['timezone'] = $userTimezone;
        } else {
            $event['timezone'] = 'UTC';
        }

        $this->selectedEvent = $event;
        $this->modalType = $type;

        if ($type === 'eventCard') {
            $this->dispatch('eventCardSelected', $this->selectedEvent);
        } elseif ($type === 'calendarEvent') {
            $this->dispatch('calendarEventSelected', $this->selectedEvent);
        }

        $this->selectedEventId = $event['id'];
        $this->isPaid = $event['is_paid'];
        $this->price = $event['price'];
        $this->slots = $event['capacity'];
        $this->eventCards = Events::with('users')->get();
    }

    public function enroll()
    {
        if ($this->isPaid == 'paid') {
            $this->redirectToStripeCheckout();
        } else {
            $this->enrollFreeEvent();
        }
    }

    private function enrollFreeEvent()
    {
        if ($this->slots <= 0) {
            $this->alert('error', 'No slot available for this event!');
            return;
        }

        $user = Auth::user();
        $event = Events::find($this->selectedEventId);

        try {
            if ($event->microsoft_event_id) {
                $attendees = [
                    [
                        'email' => $user->email,
                        'name' => $user->first_name . ' ' . $user->last_name,
                    ]
                ];

                $organizerId = $event->user->microsoft_id ?? null;
                if ($organizerId) {
                    $this->microsoftGraphService->addDynamicAttendees($event, $attendees, $organizerId);
                } else {
                    throw new \Exception('Organizer Microsoft ID is missing.');
                }
            } elseif ($user->microsoft_account && !$event->microsoft_event_id) {
                $this->createCalendarEventForUser($event, $user);
            }

            Notification::create([
                'user_id' => $user->id,
                'message' => 'You successfully enrolled in the event: ' . $event->title . ' 😊.',
                'type' => 'booking',
            ]);

            $this->alert('success', 'Successfully enrolled in the event!');
        } catch (\Exception $e) {
            Log::error('Failed to enroll user in event: ' . $e->getMessage());
            $this->alert('error', 'Failed to enroll in the event.');
            return;
        }

        Booking::create([
            'event_id' => $this->selectedEventId,
            'user_id' => $user->id,
            'phone' => $user->phone,
            'payment_status' => 'free',
            'order_number' => uniqid('FREE_'),
        ]);

        CalendarEvent::create([
            'event_id' => $this->selectedEventId,
            'user_id' => $user->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if ($event && $event->capacity > 0) {
            $event->decrement('capacity');
        }

        $this->dispatch('close-modal');
    }

    private function createCalendarEventForUser($event, $user)
    {
        try {
            $attendees = [
                [
                    'email' => $user->email,
                    'name' => $user->first_name . ' ' . $user->last_name,
                ]
            ];

            $this->microsoftGraphService->createUserCalendarEvent(
                $event->title,
                $event->description,
                $event->event_date . ' ' . $event->event_time,
                Carbon::parse($event->event_date . ' ' . $event->event_time)->addHour()->toDateTimeString(),
                $event->location,
                $attendees
            );
            Log::info('Calendar event created for user successfully.');
        } catch (\Exception $e) {
            Log::error('Error creating calendar event for user: ' . $e->getMessage());
            throw $e;
        }
    }

    private function redirectToStripeCheckout()
    {
        if ($this->slots <= 0) {
            $this->alert('error', 'No slot available for this event!');
            return;
        }

        session()->put('stripe_payment_amount', $this->price);
        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => 'Event Enrollment',
                        ],
                        'unit_amount' => $this->price * 100,
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('payment.success') . '?session_id={CHECKOUT_SESSION_ID}&event_id=' . $this->selectedEventId,
                'cancel_url' => route('payment.cancel') . '?event_id=' . $this->selectedEventId,
            ]);

            return redirect($session->url);
        } catch (\Exception $e) {
            session()->flash('error', 'Unable to create Stripe Checkout session: ' . $e->getMessage());
            return redirect()->back();
        }
    }
    public function render()
    {
        return view('livewire.event-calendar');
    }
}
