<?php
use App\Models\Course;
use App\Models\Classe;
use Carbon\Carbon;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$courseId = 1;
$course = Course::find($courseId);

echo "<h1>Debug Course $courseId</h1>";

if (!$course) {
    echo "Course not found.";
    exit;
}

echo "<p><strong>Title:</strong> " . $course->title . "</p>";
echo "<p><strong>Type:</strong> " . $course->course_type . "</p>";

$classes = Classe::where('course_id', $courseId)->get();

echo "<h2>Classes (" . $classes->count() . ")</h2>";

foreach ($classes as $class) {
    echo "<div style='border:1px solid #ccc; padding:10px; margin-bottom:10px;'>";
    echo "<p><strong>ID:</strong> " . $class->id . "</p>";
    echo "<p><strong>Title:</strong> " . $class->title . "</p>";
    echo "<p><strong>Visibility:</strong> " . ($class->visibility ? 'Visible' : 'Hidden') . "</p>";
    echo "<p><strong>Booking Start:</strong> " . $class->booking_start_date . "</p>";
    echo "<p><strong>Booking End:</strong> " . $class->booking_end_date . "</p>";
    
    $now = Carbon::now();
    $start = Carbon::parse($class->booking_start_date);
    $end = Carbon::parse($class->booking_end_date);
    
    echo "<p><strong>Current Time:</strong> " . $now->toDateTimeString() . "</p>";
    
    if ($now->lt($start)) {
        echo "<p style='color:orange'>Booking Not Started Yet</p>";
    } elseif ($now->gt($end)) {
        echo "<p style='color:red'>Booking Closed</p>";
    } else {
        echo "<p style='color:green'>Booking Open</p>";
    }
    echo "</div>";
}
