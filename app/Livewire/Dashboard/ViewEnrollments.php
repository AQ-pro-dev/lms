<?php

namespace App\Livewire\Dashboard;

use App\Models\Booking;
use App\Models\Classe;
use App\Models\Events;
use Livewire\Component;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Services\MicrosoftGraphService;

class ViewEnrollments extends Component
{
    protected $microsoftGraphService;

    public function __construct()
    {
        $this->microsoftGraphService = new MicrosoftGraphService();
    }

    public $dateRange;
    public $selectedClass;
    public $selectedEvent;
    public $enrollments;
    public $booking_payment_methods;
    public $itemId;
    public $isEditing = false;
    public $editAttendeeId = null;
    public $classes = [];
    public $events = [];
    public $items = [];
    public $attendees = [];
    public $newAttendees = [];
    public $attendeeData = [];
    public $selectedModelId;
    public $selectedModelType;

    use LivewireAlert;

    protected $listeners = ['loadAttendees'];

    public function addAttendeeField()
    {
         if (empty($this->newAttendees)) {
             $this->newAttendees = [['email' => '', 'phone' => '' , 'payment_status' => 'unpaid']];
         }else{
             $this->alert('warning', 'You can only add one attendee at a time.');
         }
    }

    public function loadAttendees($itemId, $modelType)
    {
        $this->itemId = $itemId;

        if ($modelType === 'class') {
            $this->attendees = Booking::where('class_id', $this->itemId)->get()->map(function ($attendee) {
                return [
                    'id' => $attendee->id,
                    'email' => $attendee->email,
                    'phone' => $attendee->phone,
                    'payment_status' => $attendee->payment_status,
                ];
            })->toArray();
        } elseif ($modelType === 'event') {
            $this->attendees = Booking::where('event_id', $this->itemId)->get()->map(function ($attendee) {
                return [
                    'id' => $attendee->id,
                    'email' => $attendee->email,
                    'phone' => $attendee->phone,
                    'payment_status' => $attendee->payment_status,
                ];
            })->toArray();
        }
    }


    public function openAttendeeModal($id, $modelType)
    {
        $this->selectedModelId = $id;
        $this->selectedModelType = $modelType;

        if ($modelType === 'class') {
            $this->attendees = Booking::where('class_id', $id)->with('user')->get();
        } elseif ($modelType === 'event') {
            $this->attendees = Booking::where('event_id', $id)->with('user')->get();
        }

        $this->dispatch('open-attendee-modal');
    }

    public function enableEdit($attendeeId)
    {
        $this->editAttendeeId = $attendeeId;
        $this->isEditing = true;

        // Find the attendee in the `attendees` array
        $attendee = collect($this->attendees)->firstWhere('id', $attendeeId);

        if ($attendee) {
            $this->attendeeData = [
                'email' => $attendee['email'],
                'phone' => $attendee['phone'],
                'payment_status' => $attendee['payment_status'],
            ];
        } else {
            $this->alert('error', 'Attendee not found.');
            $this->isEditing = false;
            $this->editAttendeeId = null;
        }
    }

   public function saveAttendee($attendeeId)
    {
        $attendee = Booking::find($attendeeId);

        if ($attendee && $this->attendeeData) {
            $attendee->update([
                'email' => $this->attendeeData['email'],
                'phone' => $this->attendeeData['phone'],
                'payment_status' => $this->attendeeData['payment_status'],
            ]);

            if ($this->selectedModelType == 'class') {
                $class = Classe::find($this->selectedModelId);
                if ($class && $class->microsoft_event_id) {
                    $attendees = [
                        [
                            'email' => $this->attendeeData['email'],
                            'name' => $this->attendeeData['name'] ?? 'Attendee',
                        ]
                    ];
                   try {
                        $this->microsoftGraphService->updateDynamicAttendees($class, $attendees, Auth::user()->microsoft_id);
                        $this->alert('success', 'Attendee updated successfully in Microsoft event.');
                    } catch (\Exception $e) {
                        $this->alert('error', 'Failed to update attendee in Microsoft event.');
                    }
                }
            } elseif ($this->selectedModelType == 'event') {
                $event = Events::find($this->selectedModelId);
                if ($event && $event->microsoft_event_id) {
                    $attendees = [
                        [
                            'email' => $this->attendeeData['email'],
                            'name' => $this->attendeeData['name'] ?? 'Attendee',
                        ]
                    ];
                    try {
                        $this->microsoftGraphService->updateDynamicAttendees($event, $attendees, Auth::user()->microsoft_id);
                        $this->alert('success', 'Attendee updated successfully in Microsoft event.');
                    } catch (\Exception $e) {
                        $this->alert('error', 'Failed to update attendee in Microsoft event.');
                    }
                }
            }

        }

        $this->isEditing = false;
        $this->editAttendeeId = null;
        $this->loadAttendees($this->selectedModelId, $this->selectedModelType);
    }

    public function deleteAttendee($attendeeId)
    {
        $attendee = Booking::find($attendeeId);
        if ($attendee) {
            $attendee->delete();
        }

        if ($this->selectedModelType == 'class') {
                $class = Classe::find($this->selectedModelId);
                if ($class && $class->microsoft_event_id) {
                    $attendees = [
                        [
                            'email' => $attendee->email,
                            'name' => $attendee->name ?? 'Attendee',
                        ]
                    ];
                    try {
                        $this->microsoftGraphService->deleteDynamicAttendees($class, $attendees, Auth::user()->microsoft_id);
                        $this->alert('success', 'Attendee deleted successfully from Microsoft event.');
                    } catch (\Exception $e) {
                        $this->alert('error', 'Failed to delete attendee from Microsoft event.');
                    }
                }
            } elseif ($this->selectedModelType == 'event') {
                $event = Events::find($this->selectedModelId);
                if ($event && $event->microsoft_event_id) {
                    $attendees = [
                        [
                            'email' => $attendee->email,
                            'name' => $attendee->name ?? 'Attendee',
                        ]
                    ];
                    try {
                        $this->microsoftGraphService->deleteDynamicAttendees($event, $attendees, Auth::user()->microsoft_id);
                        $this->alert('success', 'Attendee deleted successfully from Microsoft event.');
                    } catch (\Exception $e) {
                        $this->alert('error', 'Failed to delete attendee from Microsoft event.');
                    }
                }
            }

       $this->loadAttendees($this->selectedModelId, $this->selectedModelType);
    }

    public function mount()
    {
        $classes = Classe::where('user_id', Auth::id())->get();
        $events = Events::where('user_id', Auth::id())->get();

        $this->items = $classes->concat($events)->sortBy(function ($item) {
            return $item->booking_start_date;
        });

        $userTimezone = Auth::user()->timezone;

        $this->items = $this->items->map(function ($item) use ($userTimezone) {
            return [
                'id' => $item->id,
                'title' => $item->title,
                'type' => $item instanceof \App\Models\Classe ? 'Class' : 'Event',
                'date' => $item->class_date
                    ? \Carbon\Carbon::parse($item->class_date, 'UTC')->setTimezone($userTimezone)->format('F j, Y')
                    : 'Not Set',
                'time' => $item->class_time
                    ? \Carbon\Carbon::parse($item->class_time, 'UTC')->setTimezone($userTimezone)->format('h:i A')
                    : 'Not Set',
                'price' => $item->price ? '$' . number_format($item->price, 2) : 'Free',
                'model_type' => $item instanceof \App\Models\Classe ? 'class' : 'event',
            ];
        });
        $this->enrollments();
    }

    public function updatedDateRange()
    {
        if ($this->dateRange) {
            // Split the date range into start and end dates
            $dates = explode(' to ', $this->dateRange);

            if (count($dates) !== 2) {
                session()->flash('error', 'Invalid date range format.');
                return;
            }

            // Parse start and end dates
            $startDate = Carbon::parse($dates[0])->startOfDay();
            $endDate = Carbon::parse($dates[1])->endOfDay();

            // Query Classes
            $classes = Classe::whereDate('class_date', '>=', $startDate)
                ->whereDate('class_date', '<=', $endDate)
                ->where('user_id', Auth::id())
                ->orderBy('class_date', 'asc')
                ->get();

            // Query Events
            $events = Events::whereDate('class_date', '>=', $startDate)
                ->whereDate('class_date', '<=', $endDate)
                ->where('user_id', Auth::id())
                ->orderBy('class_date', 'asc')
                ->get();

            // Combine and sort results by `class_date`
            $this->items = $classes->concat($events)->sortBy(function ($item) {
                return $item->class_date;
            });

            // Map items to the desired format
            $this->items = $this->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'type' => $item instanceof \App\Models\Classe ? 'Class' : 'Event',
                    'date' => $item->class_date,
                    'time' => $item->class_time,
                    'price' => $item->price ? '$' . number_format($item->price, 2) : 'Free',
                    'model_type' => $item instanceof \App\Models\Classe ? 'class' : 'event',
                ];
            });
        }
    }

    public function enrollments()
    {
        $userTimezone = Auth::user()->timezone;

        $this->enrollments = Booking::where('user_id', Auth::id())
            ->where(function ($query) {
                $query->whereNotNull('class_id')
                    ->orWhereNotNull('event_id');
            })
            ->with(['class', 'event'])
            ->get()
            ->map(function ($enrollment) use ($userTimezone) {
                $enrollment->order_number = $enrollment->order_number;
                $enrollment->title = $enrollment->class_id
                    ? $enrollment->class->title
                    : ($enrollment->event_id ? $enrollment->event->title : 'No Enrollment');
                $enrollment->payment_status = $enrollment->payment_status === 'paid' ? 'Paid' : 'Free';
                $enrollment->time = $enrollment->class_id
                    ? \Carbon\Carbon::parse($enrollment->class->class_time, 'UTC')->setTimezone($userTimezone)->format('h:i A')
                    : ($enrollment->event_id
                        ? \Carbon\Carbon::parse($enrollment->event->class_time, 'UTC')->setTimezone($userTimezone)->format('h:i A')
                        : 'N/A');
                $enrollment->date = $enrollment->class_id
                    ? \Carbon\Carbon::parse($enrollment->class->class_date, 'UTC')->setTimezone($userTimezone)->format('F j, Y')
                    : ($enrollment->event_id
                        ? \Carbon\Carbon::parse($enrollment->event->class_date, 'UTC')->setTimezone($userTimezone)->format('F j, Y')
                        : 'N/A');
                return $enrollment;
            });
    }

    public function selectItem($itemId, $modelType)
    {
        if ($modelType === 'class') {
            $this->selectedClass = Classe::findOrFail($itemId);
            $this->selectedEvent = null;
        } elseif ($modelType === 'event') {
            $this->selectedEvent = Events::findOrFail($itemId);
            $this->selectedClass = null;
        } else {
            session()->flash('error', 'Invalid model type selected.');
        }
    }

    public function editItem()
    {

        if ($this->selectedClass) {

            return redirect()->route('dashboard.create.class', ['classId' => $this->selectedClass->id]);
        } elseif ($this->selectedEvent) {

            return redirect()->route('dashboard.create.event', ['eventId' => $this->selectedEvent->id]);
        } else {

            session()->flash('error', 'No item selected for editing.');
        }
        $this->dispatch('close-modal');
    }

    public function deleteItem()
    {

        if ($this->selectedClass) {
            $this->selectedClass->delete();
            $this->classes = Classe::all();
            $this->selectedClass = null;
            $this->alert('success', 'Class deleted successfully.');
            $this->dispatch('close-modal');
        } elseif ($this->selectedEvent) {
            $this->selectedEvent->delete();
            $this->events = Events::all();
            $this->selectedEvent = null;
            $this->alert('success', 'Event deleted successfully.');
            $this->dispatch('close-modal');
        } else {
            $this->alert('error', 'No item selected for deletion.');
        }
    }

    public function saveManualAttendees()
    {
        // dd($this->selectedModelId);
     foreach ($this->newAttendees as $newAttendee) {
                $validatedData = \Validator::make($newAttendee, [
            'email' => 'required|email',
            'phone' => 'required|string|max:15',
            'payment_status' => 'required',
        ])->validate();
        Booking::create([
            'class_id' => $this->selectedModelType == 'class' ? $this->selectedModelId : null,
            'event_id' => $this->selectedModelType == 'event' ? $this->selectedModelId : null,
            'email' => $newAttendee['email'],
            'phone' => $newAttendee['phone'],
            'payment_status' => $newAttendee['payment_status'],
            'type' => 'manual',
            'order_number' => 'MAN_' . strtoupper(uniqid()), // Generate order number
            'created_by' => Auth::id(),
        ]);
        if ($this->selectedModelType == 'class') {
                $class = Classe::find($this->selectedModelId);
                if ($class && $class->microsoft_event_id) {
                    $attendees = [
                        [
                            'email' => $newAttendee['email'],
                            'name' => $newAttendee['name'] ?? 'Attendee',
                        ]
                    ];
                    $this->microsoftGraphService->addDynamicAttendees($class, $attendees, Auth::user()->microsoft_id);
                }
            } elseif ($this->selectedModelType == 'event') {
                $event = Events::find($this->selectedModelId);
                if ($event && $event->microsoft_event_id) {
                    $attendees = [
                        [
                            'email' => $newAttendee['email'],
                            'name' => $newAttendee['name'] ?? 'Attendee',
                        ]
                    ];
                    $this->microsoftGraphService->addDynamicAttendees($event, $attendees, Auth::user()->microsoft_id);
                }
            }
     }

     $this->newAttendees = [];
     $this->alert('success', 'Manual attendees saved successfully.');
     $this->loadAttendees($this->selectedModelId, $this->selectedModelType);

    }

    public function createQuiz()
    {
        if ($this->selectedClass && $this->selectedClass->class_type == 'recorded') {
            $this->dispatch('close-modal');
            return redirect()->route('quizzes.create', ['class_id' => $this->selectedClass->id]);
        }
    }

    public function render()
    {
        return view('livewire.dashboard.view-enrollments')->layout('components.layouts.dashboard');
    }
}
