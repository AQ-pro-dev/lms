<?php

namespace App\Livewire;

use App\Models\Booking;
use App\Models\Lecture; // Make sure to import the Lecture model
use App\Models\Course;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Component;
use Stripe\Stripe;

class OnSiteCourseDetails extends Component
{
    public $courseId;
    public $courseTitle;
    public $courseDescription;
    public $learningOutcomes;
    public $targetAudience;
    public $requirements;
    public $price;
    public $isEnrolled;
    public $isPaid;
    public $course;

    public $lectures = [];
    public $showFullDescription = false;

    public function mount($id = null)
    {
        if ($id) {
            $this->courseId = $id;
            $this->loadCourseData();
            $this->loadLectures();
            $this->checkEnrollment();
        }
        $this->course = Course::findOrFail($this->courseId)->with('lectures', 'bookings', 'reviews')->first();
    }

    public function loadCourseData()
    {
        $course = Course::findOrFail($this->courseId);
        // dd($course);

        $this->courseTitle = $course->title;
        $this->courseDescription = $course->description;
        $this->learningOutcomes = $course->learning_outcomes;
        $this->targetAudience = $course->target_audience;
        $this->requirements = $course->requirements;
        $this->price = (float) $course->price;
        $this->isPaid = $this->price > 0;
    }

    public function loadLectures()
    {
        $this->lectures = Lecture::where('course_id', $this->courseId)->get();
    }

    public function enrollCourse()
    {
        if ($this->isPaid) {
            $this->redirectToStripeCheckout();
        } else {
            $this->processFreeEnrollment();
        }
    }

    private function processFreeEnrollment()
    {
        Booking::create([
            'course_id' => $this->courseId,
            'class_id' => null,
            'phone_number' => Auth::user()->phone,
            'user_id' => Auth::id(),
            'payment_status' => 'free',
            'order_number' => uniqid('FREE_'),
        ]);
        Notification::create([
            'user_id' => Auth::id(),
            'message' => 'You successfully Enrolled in the free course: ' . $this->courseTitle . ' 😊.',
            'type' => 'booking',
        ]);
        session()->flash('success', 'You have successfully enrolled in the free course!');
        return redirect()->to(route('onsite.course.details', ['id' => $this->courseId]));
    }

    private function redirectToStripeCheckout()
    {
        session()->put('stripe_payment_amount', $this->price);
        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => $this->courseTitle,
                        ],
                        'unit_amount' => intval($this->price * 100), // Convert to cents
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('payment.success') . '?session_id={CHECKOUT_SESSION_ID}&course_id=' . $this->courseId,
                'cancel_url' => route('payment.cancel') . '?course_id=' . $this->courseId,
            ]);

            return redirect()->to($session->url);
        } catch (\Exception $e) {
            session()->flash('error', 'There was an error processing your payment: ' . $e->getMessage());
            return redirect()->back();
        }
    }
    public function checkEnrollment()
    {
        $this->isEnrolled = Booking::where('user_id', Auth::id())
            ->where('course_id', $this->courseId)
            ->exists();
    }


    public function toggleDescription()
    {
        $this->showFullDescription = !$this->showFullDescription;
    }

    public function render()
    {
        return view('livewire.on-site-course-details');
    }
}
