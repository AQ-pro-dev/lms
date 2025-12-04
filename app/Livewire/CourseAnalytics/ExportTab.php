<?php

namespace App\Livewire\CourseAnalytics;

use App\Models\Booking;
use App\Models\Course;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class ExportTab extends Component
{
    public function exportToCSV()
    {
        // Fetch courses created by the logged-in user
        $courses = Course::where('user_id', Auth::id())
            ->where('is_published', true)
            ->where('is_drafted', false)
            ->pluck('id'); // Get course IDs

        // Fetch bookings for those courses
        $bookings = Booking::whereIn('course_id', $courses)
            ->with('user', 'course') // Include related data
            ->get();

        // Prepare the CSV header
        $csvHeader = ['Student Name', 'Email', 'Course Title', 'Registration Date', 'Progress'];

        // Prepare the CSV data
        $csvData = $bookings->map(function ($booking) {
            $progress = $booking->course->course_type === 'recorded'
                ? $booking->user->progress()->where('course_id', $booking->course_id)->value('progress') . '%'
                : 'N/A';

            return [
                '"' . str_replace('"', '""', $booking->user->first_name . ' ' . $booking->user->last_name) . '"',  // Escape quotes
                '"' . str_replace('"', '""', $booking->user->email) . '"',
                '"' . str_replace('"', '""', $booking->course->title) . '"',
                $booking->created_at->format('Y-m-d'),
                $progress,
            ];
        });

        // Combine the header and data
        $csv = collect([$csvHeader])->merge($csvData);

        // Generate the CSV content
        $csvContent = $csv->map(fn($row) => implode(',', $row))->implode("\n");

        // Define a file name
        $fileName = 'students_report.csv';

        // Store the CSV content temporarily and then return as download
        $tempPath = storage_path('app/public/' . $fileName);
        file_put_contents($tempPath, $csvContent);

        // Return the file as a download response
        return response()->download($tempPath, $fileName)->deleteFileAfterSend(true);
    }

    public function render()
    {
        return view('livewire.course-analytics.export-tab');
    }
}
