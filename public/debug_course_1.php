<?php
use App\Models\Course;
use App\Models\Classe;
use App\Models\Lecture;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$id = 1;
$course = Course::find($id);

if (!$course) {
    die("### COURSE NOT FOUND ###");
}

echo "\n### DEBUG START ###\n";
echo "ID: " . $course->id . "\n";
echo "Type: [" . ($course->course_type ?? 'NULL') . "]\n";
echo "Classes: " . Classe::where('course_id', $id)->count() . "\n";
echo "Lectures: " . Lecture::where('course_id', $id)->count() . "\n";
echo "### DEBUG END ###\n";
