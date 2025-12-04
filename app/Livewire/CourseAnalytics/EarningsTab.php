<?php

namespace App\Livewire\CourseAnalytics;

use App\Models\Course;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class EarningsTab extends Component
{
    public $totalEarnings;
    public $totalSales;
    public $totalWithdrawals;
    public $totalCommissions;
    public $totalFees;
    public $monthlyEarnings = []; // Data for the chart
    public $chartLabels;
    public $chartData;

    public function mount()
    {
        $user = Auth::user();

        $courses = Course::where('user_id', $user->id)
            ->where('is_published', true)
            ->where('is_drafted', false)
            ->get();

        $totalEarnings = 0;
        $totalSales = 0;
        $totalWithdrawals = 0;
        $totalCommissions = 0;
        $totalFees = 0;

        $earningsByMonth = array_fill(1, 12, 0);

        foreach ($courses as $course) {
            $bookings = $course->bookings;

            foreach ($bookings as $booking) {
                $payments = $booking->payments;
                $paymentSum = $payments->sum('amount');

                $totalEarnings += $paymentSum;
                $totalSales += $paymentSum;
                $totalWithdrawals += $paymentSum;

                foreach ($payments as $payment) {
                    $month = $payment->created_at->month;
                    $earningsByMonth[$month] += $payment->amount;
                }
            }
        }

        $this->totalEarnings = $totalEarnings;
        $this->totalSales = $totalSales;
        $this->totalWithdrawals = $totalWithdrawals;
        $this->totalCommissions = $totalCommissions;
        $this->totalFees = $totalFees;

        $this->monthlyEarnings = array_values($earningsByMonth);
        $this->chartLabels = range(1, 12);
        $this->chartData = $this->monthlyEarnings;
        $this->dispatch('renderChart', $this->chartLabels, $this->monthlyEarnings);
    }



    public function render()
    {
        return view('livewire.course-analytics.earnings-tab');
    }
}
