<?php

namespace App\Livewire\Dashboard;

use App\Services\MicrosoftGraphService;
use App\Models\CalendarEvent;
use App\Models\Course;
use App\Models\Classe;
use App\Models\Events;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class ClassForm extends Component
{
    use LivewireAlert;

    protected $microsoftGraphService;
    public function __construct()
    {
        $this->microsoftGraphService = app(MicrosoftGraphService::class);
    }

    public $type = 'class';
    public $course_id, $title, $description, $class_type = 'onsite', $teams_link, $onsite_address, $is_paid = 'free';
    public $price = 0, $capacity, $visibility = 1, $booking_start_date, $booking_end_date, $date_range, $class_date, $class_time;
    public $courses = [];
    public $classId;
    public $eventId;

    protected $rules = [
        'type' => 'required|in:class,event',
        'course_id' => 'nullable|exists:courses,id',
        'title' => 'required|string|min:5|max:30|unique:classes,title',
        'description' => 'required|string|min:10|max:100',
        'class_type' => 'required_if:type,class|in:onsite,virtual',
        'teams_link' => 'nullable|url',
        'onsite_address' => 'nullable|required_if:class_type,onsite|string|max:255',
        'is_paid' => 'required|in:free,paid',
        'price' => 'nullable|required_if:is_paid,paid|numeric',
        'capacity' => 'required|integer|min:1',
        'class_date' => 'required|date|date_format:Y-m-d',
        'class_time' => 'required',
        'date_range' => 'required',
        'booking_start_date' => 'required|date',
        'booking_end_date' => 'required|date|before:class_date',
    ];

    protected $messages = [
        'title.required' => 'Please provide a title for the class/event.',
        'booking_start_date.after_or_equal' => 'The booking start date must be today or later.',
        'booking_end_date.after' => 'The booking end date must be after the start date.',
        'booking_end_date' => 'The booking end date must be before the actual class date.',
    ];

    public function mount($classId = null, $eventId = null)
    {
        if ($classId) {
            $this->classId = $classId;
            $this->type = 'class';
            $this->loadClassData();
        } elseif ($eventId) {
            $this->eventId = $eventId;
            $this->type = 'event';
            $this->loadEventData();
        }
        $this->loadCourses();
    }

    public function updatedDateRange($value)
    {
        $dates = explode(' to ', $value);
        if (count($dates) === 2) {
            try {
                $this->booking_start_date = Carbon::parse($dates[0])->toDateString();
                $this->booking_end_date = Carbon::parse($dates[1])->toDateString();
            } catch (\Exception $e) {
                $this->reset(['booking_start_date', 'booking_end_date']);
                $this->addError('date_range', 'Invalid date range format.');
            }
        } else {
            $this->reset(['booking_start_date', 'booking_end_date']);
        }
    }

    public function loadClassData()
    {
        $class = Classe::find($this->classId);
        if ($class) {
            $this->type = 'class';
            $this->course_id = $class->course_id;
            $this->title = $class->title;
            $this->description = $class->description;
            $this->class_type = $class->class_type;
            $this->teams_link = $class->teams_link;
            $this->onsite_address = $class->class_type === 'onsite' ? $class->onsite_address : null;
            $this->is_paid = $class->is_paid;
            $this->price = $class->price;
            $this->capacity = $class->capacity;
            $this->visibility = $class->visibility;

            $userTimezone = Auth::user()->timezone;
            $this->booking_start_date = Carbon::parse($class->booking_start_date, 'UTC')->setTimezone($userTimezone)->toDateString();
            $this->booking_end_date = Carbon::parse($class->booking_end_date, 'UTC')->setTimezone($userTimezone)->toDateString();
            $this->date_range = $this->booking_start_date . ' to ' . $this->booking_end_date;
            $this->class_date = Carbon::parse($class->class_date, 'UTC')->setTimezone($userTimezone)->toDateString();
            $this->class_time = Carbon::parse($class->class_time, 'UTC')->setTimezone($userTimezone)->format('H:i');
        }
    }

    public function loadEventData()
    {

        $event = Events::find($this->eventId);
        if ($event) {
            $this->type = 'event';
            $this->course_id = $event->course_id;
            $this->title = $event->title;
            $this->description = $event->description;
            $this->class_type = $event->class_type;
            $this->teams_link = $event->teams_link;
            $this->onsite_address = $event->class_type === 'onsite' ? $event->onsite_address : null;
            $this->is_paid = $event->is_paid;
            $this->price = $event->price;
            $this->capacity = $event->capacity;

            $userTimezone = Auth::user()->timezone;
            $this->booking_start_date = Carbon::parse($event->booking_start_date, 'UTC')->setTimezone($userTimezone)->toDateString();
            $this->booking_end_date = Carbon::parse($event->booking_end_date, 'UTC')->setTimezone($userTimezone)->toDateString();
            $this->date_range = $this->booking_start_date . ' to ' . $this->booking_end_date;
            $this->class_date = Carbon::parse($event->class_date, 'UTC')->setTimezone($userTimezone)->toDateString();
            $this->class_time = Carbon::parse($event->class_time, 'UTC')->setTimezone($userTimezone)->format('H:i');
        }
    }

    public function updateClass()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'class_date' => 'required|date',
            'class_time' => 'required|date_format:H:i',
            'booking_start_date' => 'required|date',
            'booking_end_date' => 'required|date|after:booking_start_date|before:class_date',
            'price' => 'nullable|numeric|min:0',
            'capacity' => 'nullable|integer|min:1',
            'class_type' => 'required|string|in:virtual,onsite,recorded',
            'onsite_address' => 'nullable|string|max:255',
            'teams_link' => 'nullable|url',
        ]);
        $userTimezone = Auth::user()->timezone;

        $classDateTime = Carbon::parse($this->class_date . ' ' . $this->class_time, $userTimezone);
        $utcClassDateTime = $classDateTime->setTimezone('UTC');

        $bookingStartDate = Carbon::parse($this->booking_start_date, $userTimezone)->setTimezone('UTC');
        $bookingEndDate = Carbon::parse($this->booking_end_date, $userTimezone)->setTimezone('UTC');

        if ($this->classId) {
            $class = Classe::findOrFail($this->classId);
            $class->update([
                'course_id' => $this->course_id,
                'title' => $this->title,
                'description' => $this->description,
                'class_type' => $this->class_type,
                'teams_link' => $this->teams_link,
                'onsite_address' => $this->onsite_address,
                'is_paid' => $this->is_paid,
                'price' => $this->price,
                'capacity' => $this->capacity,
                'visibility' => $this->visibility,
                'class_date' => $utcClassDateTime->toDateString(),
                'class_time' => $utcClassDateTime->toTimeString(),
                'booking_start_date' => $bookingStartDate->toDateString(),
                'booking_end_date' => $bookingEndDate->toDateString(),
            ]);

            $this->updateCalendarEvent($class->microsoft_event_id);

            $this->alert('success', 'Class updated successfully!');
            $this->redirect(route("dashboard.enrollments"));
        } elseif ($this->eventId) {
            $event = Events::findOrFail($this->eventId);
            $event->update([
                'course_id' => $this->course_id,
                'title' => $this->title,
                'description' => $this->description,
                'class_type' => $this->class_type,
                'teams_link' => $this->teams_link,
                'onsite_address' => $this->onsite_address,
                'is_paid' => $this->is_paid,
                'price' => $this->price,
                'capacity' => $this->capacity,
                'visibility' => $this->visibility,
                'class_date' => $utcClassDateTime->toDateString(),
                'class_time' => $utcClassDateTime->toTimeString(),
                'booking_start_date' => $bookingStartDate->toDateString(),
                'booking_end_date' => $bookingEndDate->toDateString(),
            ]);

            $this->updateCalendarEvent($event->microsoft_event_id);

            $this->alert('success', 'Event updated successfully!');
            $this->redirect(route("dashboard.enrollments"));
        }
    }

    private function updateCalendarEvent($microsoftEventId)
    {
        if ($microsoftEventId) {
            $attendees = [
                [
                    'email' => Auth::user()->email,
                    'name' => Auth::user()->first_name . ' ' . Auth::user()->last_name,
                ]
            ];

            $this->microsoftGraphService->updateCalendarEvent(
                $microsoftEventId,
                $this->title,
                $this->description,
                $this->class_date . ' ' . $this->class_time,
                Carbon::parse($this->class_date . ' ' . $this->class_time)->addHour()->toDateTimeString(),
                $this->onsite_address,
                $attendees,
                $this->class_type === 'virtual'
            );
        }
    }

    public function loadCourses()
    {
        $this->courses = Course::with(['user', 'tutors'])
            ->where('is_published', true)
            ->where('is_drafted', false)
            ->where('course_type', 'classtype')
            ->where('user_id', Auth::id())
            ->get();
    }

    private function createClass()
    {
        $userTimezone = Auth::user()->timezone;
        $classDateTime = Carbon::parse($this->class_date . ' ' . $this->class_time, $userTimezone);
        $utcClassDateTime = $classDateTime->setTimezone('UTC');

        $class = Classe::create([
            'title' => $this->title,
            'description' => $this->description,
            'course_id' => $this->course_id,
            'user_id' => Auth::id(),
            'class_type' => $this->class_type,
            'teams_link' => $this->teams_link,
            'onsite_address' => $this->onsite_address,
            'is_paid' => $this->is_paid,
            'price' => $this->price,
            'capacity' => $this->capacity,
            'visibility' => $this->visibility,
            'class_date' => $utcClassDateTime->toDateString(),
            'class_time' => $utcClassDateTime->toTimeString(),
            'booking_start_date' => $this->booking_start_date,
            'booking_end_date' => $this->booking_end_date,
        ]);

        CalendarEvent::create([
            'user_id' => Auth::id(),
            'class_id' => $class->id,
        ]);

        if (Auth::user()->microsoft_account == true) {
            $attendees = [
                ['email' => Auth::user()->email, 'name' => Auth::user()->first_name . ' ' . Auth::user()->last_name],
            ];
            $graphService = new MicrosoftGraphService();
            $success = false;
            if ($class->class_type == 'virtual') {
                $success = $graphService->createCalendarEventWithTeamsMeeting($class, $attendees) ? true : false;
            } else {
                $success = $graphService->createCalendarEvent($class) ? true : false;
            }
            if ($success) {
                $this->alert('success', 'Class created successfully!');
                $this->redirect(route("dashboard.enrollments"));
            } else {
                $this->alert('error', 'Failed to create class.');
            }
        } else {
            $this->alert('success', 'Class created successfully!');
            $this->redirect(route("dashboard.enrollments"));
        }
    }

    private function createEvent()
    {
        $userTimezone = Auth::user()->timezone;
        $eventDateTime = Carbon::parse($this->class_date . ' ' . $this->class_time, $userTimezone);
        $utcEventDateTime = $eventDateTime->setTimezone('UTC');

        $event = Events::create([
            'title' => $this->title,
            'description' => $this->description,
            'user_id' => Auth::id(),
            'class_type' => $this->class_type,
            'teams_link' => $this->teams_link,
            'onsite_address' => $this->onsite_address,
            'is_paid' => $this->is_paid,
            'price' => $this->price,
            'capacity' => $this->capacity,
            'visibility' => $this->visibility,
            'class_date' => $utcEventDateTime->toDateString(),
            'class_time' => $utcEventDateTime->toTimeString(),
            'booking_start_date' => $this->booking_start_date,
            'booking_end_date' => $this->booking_end_date,
        ]);

        CalendarEvent::create([
            'user_id' => Auth::id(),
            'event_id' => $event->id,
        ]);

        if (Auth::user()->microsoft_account == true) {
            $attendees = [
                ['email' => Auth::user()->email, 'name' => Auth::user()->first_name . ' ' . Auth::user()->last_name],
            ];
            $graphService = new MicrosoftGraphService();
            $success = false;

            if ($event->class_type == 'virtual') {
                $success = $graphService->createCalendarEventWithTeamsMeeting($event, $attendees) ? true : false;
            } else {
                $success = $graphService->createCalendarEvent($event) ? true : false;
            }
            if ($success) {
                $this->alert('success', 'Event created successfully!');
                $this->redirect(route("calendar"));
            } else {
                $this->alert('error', 'Failed to create class.');
            }
        } else {
            $this->alert('success', 'Event created successfully!');
            $this->redirect(route("dashboard.enrollments"));
        }
    }

    public function save()
    {
        if ($this->type === 'class') {
            if ($this->classId) {
                $this->updateClass();
            } else {
                $this->createClass();
            }
        } elseif ($this->type === 'event') {
            if ($this->eventId) {
                $this->updateClass();
            } else {
                $this->createEvent();
            }
        }
    }

    public function dispatchTypeChanged()
    {
        if ($this->type === 'class') {
            $this->course_id = null;
            $this->description = null;
        } elseif ($this->type === 'event') {
            $this->course_id = null;
        }
    }

    public function dispatchClassTypeChanged()
    {
        if ($this->class_type === 'virtual') {
            $this->onsite_address = null;
        } elseif ($this->class_type === 'onsite') {
            $this->date_range = null;
        }
    }

    public function dispatchIsPaidChanged()
    {
        if ($this->is_paid === 'free') {
            $this->price = null;
        }
    }



    public function render()
    {
        return view('livewire.dashboard.class-form')->layout('components.layouts.createCourse');
    }
}
