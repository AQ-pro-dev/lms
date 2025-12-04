<?php

namespace App\Livewire\Dashboard;

use App\Models\Email;
use Livewire\Component;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Emails extends Component
{
    public $emails;

    public function mount()
    {
        // Fetch all emails from the emails table
        $this->emails = Email::all();
    }

    // Method to export emails as CSV
    public function exportCsv()
    {
        // Prepare data for CSV export
        $csvData = $this->emails->map(function ($email) {
            return [$email->email];
        });

        $csvData->prepend(['Email']); // Add column header

        $response = new StreamedResponse(function () use ($csvData) {
            $handle = fopen('php://output', 'w');
            foreach ($csvData as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="emails.csv"');
        return $response;
    }

    public function render()
    {
        return view('livewire.dashboard.emails')->layout('components.layouts.dashboard');
    }
}
