<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\CalendarEvent;
use App\Models\Classe;
use App\Models\Course;
use App\Models\Notification;
use App\Models\Events;
use App\Models\Payment;
use App\Services\MicrosoftGraphService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{

    protected $microsoftGraphService;

    public function __construct(MicrosoftGraphService $microsoftGraphService)
    {
        $this->microsoftGraphService = $microsoftGraphService;
    }



    public function success(Request $request)
    {
        $userId = Auth::id();
        $user = Auth::user();
        $amount = session('stripe_payment_amount'); //retrieve the session value
        $sessionId = $request->query('session_id');
        $classId = $request->query('class_id'); //class payments
        $courseId = $request->query('course_id'); //course payments
        $eventId = $request->query('event_id'); //event payments

        $courseName = Course::where('id', $courseId)->value('title');
        $className = Classe::where('id', $classId)->value('title');
        $eventName = Events::where('id', $eventId)->value('title');
        // dd($courseName);

        if ($courseId) {
            //handle Course Enrollment
            $booking = Booking::create([
                'course_id' => $courseId,
                'user_id' => $userId,
                'phone_number' => $user->phone,
                'class_id' => null,
                'payment_status' => 'paid',
                'order_number' => uniqid('PAID_COURSE_'),
            ]);

            Payment::create([
                'amount' => $amount,
                'booking_id' => $booking->id,
                'payment_method' => 'stripe',
                'transaction_id' => $sessionId,
            ]);
            Notification::create([
                'user_id' => $userId,
                'message' => 'You successfully purchased the course: ' . $courseName . ' 😊.',
                'type' => 'booking',
            ]);

            return redirect()->route("onsite.course.details", $courseId)
                ->with('success', 'Payment successful! You are enrolled in the course.');
        } elseif ($classId) {
            $class = Classe::findOrFail($classId);

            try {
                if ($class->microsoft_event_id) {
                    $attendees = [
                        [
                            'email' => $user->email,
                            'name' => $user->first_name . ' ' . $user->last_name,
                        ]
                    ];

                    $organizerId = $class->user->microsoft_id;
                    $this->microsoftGraphService->addDynamicAttendees($class, $attendees, $organizerId);
                } elseif ($user->microsoft_account && !$class->microsoft_event_id) {
                    $this->createCalendarEventForUser($class, $user);
                }

                $booking = Booking::create([
                    'class_id' => $classId,
                    'course_id' => null,
                'phone_number' => $user->phone,
                    'user_id' => $userId,
                    'payment_status' => 'paid',
                    'order_number' => uniqid('PAID_CLASS_'),
                ]);

                Payment::create([
                    'amount' => $amount,
                    'booking_id' => $booking->id,
                    'payment_method' => 'stripe',
                    'transaction_id' => $sessionId,
                ]);
                Notification::create([
                    'user_id' => $userId,
                    'message' => 'You successfully purchased the course: ' . $className . ' 😊.',
                    'type' => 'booking',
                ]);

                // Reduce class capacity
                Classe::where('id', $classId)->decrement('capacity');

                CalendarEvent::create([
                    'user_id' => $userId,
                    'class_id' => $classId,
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to enroll user in class: ' . $e->getMessage());
            }


            return redirect()->route("course.details", $classId)
                ->with('success', 'Payment successful! You are enrolled in the class.');
        } elseif ($eventId) {
            $event = Events::findOrFail($eventId);

            try {
                if ($event->microsoft_event_id) {
                    $attendees = [
                        [
                            'email' => $user->email,
                            'name' => $user->first_name . ' ' . $user->last_name,
                        ]
                    ];

                    $organizerId = $event->users->microsoft_id;
                    $this->microsoftGraphService->addDynamicAttendees($event, $attendees, $organizerId);
                } elseif ($user->microsoft_account && !$event->microsoft_event_id) {
                    $this->createCalendarEventForUser($event, $user);
                }

                $booking = Booking::create([
                    'event_id' => $eventId,
                    'user_id' => $userId,
                    'phone_number' => $user->phone,
                    'payment_status' => 'paid',
                    'order_number' => uniqid('PAID_EVENT_'),
                ]);

                Payment::create([
                    'amount' => $amount,
                    'booking_id' => $booking->id,
                    'payment_method' => 'stripe',
                    'transaction_id' => $sessionId,
                ]);

                Notification::create([
                    'user_id' => $userId,
                    'message' => 'You successfully purchased the event: ' . $eventName . ' 😊.',
                    'type' => 'booking',
                ]);

                // Optionally reduce available slots in the event if needed
                Events::where('id', $eventId)->decrement('capacity');

                CalendarEvent::create([
                    'user_id' => $userId,
                    'event_id' => $eventId,
                ]);

                return redirect()->route("calendar")
                    ->with('success', 'Payment successful! You are enrolled in the event.');
            } catch (\Exception $e) {
                Log::error('Failed to enroll user in event: ' . $e->getMessage());
                return redirect()->route("calendar")
                    ->with('error', 'Failed to enroll in the event.');
            }
        }

        return redirect()->route('dashboard')
            ->with('error', 'Payment failed. Please try again.');
    }

    public function cancel()
    {
        return redirect()->route('dashboard')->with('error', 'Payment canceled. Please try again.');
    }
    public function certifictes()
    {
        return view('certificates.dummy');
    }
    private function createCalendarEventForUser($class, $user)
    {
        try {
            $attendees = [
                [
                    'email' => $user->email,
                    'name' => $user->first_name . ' ' . $user->last_name,
                ]
            ];

            $this->microsoftGraphService->createUserCalendarEvent(
                $class->title,
                $class->description,
                $class->class_date . ' ' . $class->class_time,
                Carbon::parse($class->class_date . ' ' . $class->class_time)->addHour()->toDateTimeString(),
                $class->onsite_address,
                $attendees
            );
            Log::info('Calendar event created for user successfully.');
        } catch (\Exception $e) {
            Log::error('Error creating calendar event for user: ' . $e->getMessage());
            throw $e;
        }
    }
}
