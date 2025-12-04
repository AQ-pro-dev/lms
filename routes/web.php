<?php

use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\MicrosoftAuthController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\PaymentController;
use App\Livewire\CourseCatelogue;
use App\Livewire\AboutUs;
use App\Livewire\CourseAnalytics\CourseDetail;
use App\Livewire\CourseDetails;
use App\Livewire\Dashboard\Announcements;
use App\Livewire\Dashboard\Category;
use App\Livewire\Dashboard\ClassForm;
use App\Livewire\Dashboard\CourseAnalytics;
use App\Livewire\Dashboard\CoursesRequest;
use App\Livewire\Dashboard\CreateCourse;
use App\Livewire\Dashboard\Dashboard;
use App\Livewire\Dashboard\HelpRequest;
use App\Livewire\Dashboard\ViewEnrollments;
use App\Livewire\Dashboard\DashboardEnrolledCourses;
use App\Livewire\Dashboard\DashboardProfile;
use App\Livewire\Dashboard\DashboardSettings;
use App\Livewire\Dashboard\DashboardWishlist;
use App\Livewire\Dashboard\HelpRequestsAdmin;
use App\Livewire\Dashboard\Lcture;
use App\Livewire\Dashboard\Lecture as DashboardLecture;
use App\Livewire\Dashboard\MyCourses;
use App\Livewire\Dashboard\ScheduleClasses;
use App\Livewire\Dashboard\Tags;
use App\Livewire\Dashboard\TutorRequest;
use App\Livewire\Dashboard\ViewLectures;
use App\Livewire\Home as LivewireHome;
use App\Livewire\Inc\DashboardFooter;
use App\Livewire\Login;
use App\Livewire\Register;
use App\Livewire\TutorPrfile;
use App\Livewire\OnSiteCourseDetails;
use App\Livewire\EventCalendar;
use App\Livewire\Quizzes\QuizCreate;
use App\Livewire\Quizzes\QuizView;
use App\Livewire\RecordedLcture;
use App\Models\Lecture;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckRole;
use App\Livewire\Dashboard\Emails;
use App\Livewire\Dashboard\Student;
use App\Livewire\Dashboard\StudentView;
use App\Livewire\RecordedLecture;

//public routes
Route::get('/', LivewireHome::class)->name('home');
Route::get('/register', Register::class)->name('register');
Route::get('/verify-email/{token}', [EmailVerificationController::class, 'verify'])->name('verify.email');
Route::get('/login', Login::class)->name('login');
Route::get('/course/catalogue', CourseCatelogue::class)->name('course.catalogue');
Route::get('/about-us', AboutUs::class)->name('about-us');
Route::get('/tutor/profile/{id?}', TutorPrfile::class)->name('tutor.profile');
Route::get('/course/details/{id?}', CourseDetails::class)->name('course.details');
Route::get('/on-site/course/details/{id?}', OnSiteCourseDetails::class)->name('onsite.course.details');
Route::get('/calendar', EventCalendar::class)->name('calendar');
// password reset
Route::post('password/reset', [PasswordResetController::class, 'reset'])->name('password.update');
Route::get('reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
//microsoft
Route::get('/auth/microsoft/callback', [MicrosoftAuthController::class, 'callback'])->name('microsoft.callback');
Route::get('/auth/microsoft', [MicrosoftAuthController::class, 'redirectToMicrosoft'])->name('microsoft.redirect');



Route::middleware(['checkrole:1'])->group(
    function () {

        Route::get('dashboard/tutor/Request', TutorRequest::class)->name('dashboard.tutor.request');
        Route::get('/dashboard/help/Request/Admin', HelpRequestsAdmin::class)->name('dashboard.help.admin');
        Route::get('dashboard/categories', Category::class)->name('dashboard.categories');
        Route::get('dashboard/tags', Tags::class)->name('dashboard.tags');
        Route::get('dashboard/coursesRequest', CoursesRequest::class)->name('dashboard.coursesRequest');
        Route::get('dashboard/emails', Emails::class)->name('dashboard.emails');
    }
);

Route::middleware('auth')->group(
    function () {
        // stripe
        Route::get('/payment/success', [PaymentController::class, 'success'])->name('payment.success');
        Route::get('/payment/cancel', [PaymentController::class, 'cancel'])->name('payment.cancel');
        Route::get('/quiz/{quizId}', QuizView::class)->name('quiz.take');
        Route::get('/recorded/lecture/{courseId}/{lectureId}', RecordedLecture::class)->name('recorded.lecture');
        // dashboard routes
        Route::get('/dashboard', Dashboard::class)->name('dashboard');
        Route::get('dashboard/help', HelpRequest::class)->name('dashboard.help');
        Route::get('/dashboard/enrollments', ViewEnrollments::class)->name('dashboard.enrollments');
        Route::get('/dashboard/profile', DashboardProfile::class)->name('dashboard.profile');
        Route::get('/dashboard/wishlist', DashboardWishlist::class)->name('dashboard.wishlist');
        Route::get('/dashboard/enrolled-courses', DashboardEnrolledCourses::class)->name('dashboard.enrolledCourses');
        Route::get('/dashboard/settings', DashboardSettings::class)->name('dashboard.settings');
        Route::get('/dashboard/students', Student::class)->name('dashboard.students');
    }
);
// Route::middleware(['checkrole:1,2'])->group(function () {
//quiz
Route::get('/quizzes/create/{lectureId}', QuizCreate::class)->name('quizzes.create');
Route::get('dashboard/view/lectures/{courseId}', ViewLectures::class)->name('dashboard.view.lectures');
Route::get('/dashboard/lectures', DashboardLecture::class)->name('dashboard.lectures');
Route::get('/dashboard/course/details', CourseDetail::class)->name('dashboard.course.details');
Route::get('/dashboard/mycourses', MyCourses::class)->name('dashboard.mycourses');
Route::get('/dashboard/announcements', Announcements::class)->name('dashboard.announcements');
Route::get('/dashboard/course-analytics', CourseAnalytics::class)->name('dashboard.analytics');
Route::get('/dashboard/courses/create/{courseId?}', CreateCourse::class)->name('dashboard.create.course');
Route::get('/dashboard/create/class/{classId?}', ClassForm::class)->name('dashboard.create.class');
Route::get('/dashboard/create/class/{eventId?}', ClassForm::class)->name('dashboard.create.event');
// });
Route::get('/dashboard/student/view/{id?}', StudentView::class)->name('dashboard.student.view');

Route::get('/certificate', [PaymentController::class, 'certifictes'])->name('certificate.index');
