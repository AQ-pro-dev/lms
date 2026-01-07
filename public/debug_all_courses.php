<?php
error_reporting(E_ERROR | E_PARSE);
use App\Models\Course;
use App\Models\Classe;

require __DIR__ . '/../vendor/autoload.php';
error_reporting(E_ERROR | E_PARSE);
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$courses = Course::all();
echo "Total Courses: " . $courses->count() . "\n";
foreach ($courses as $c) {
    $classCount = Classe::where('course_id', $c->id)->count();
    if ($classCount > 0 || $c->course_type == 'recorded') {
        echo "ID:{$c->id} | Title:{$c->title} | Type:{$c->course_type} | Classes:{$classCount}\n";
    }
}
