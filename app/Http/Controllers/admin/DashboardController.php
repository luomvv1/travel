<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\admin\DashboardModel;
use Illuminate\Http\Request;

class DashboardController extends Controller
{

    private $dashboard;

    public function __construct()
    {
        $this->dashboard = new DashboardModel();
    }
    public function index()
    {
        $title = 'Admin';

        $summary = $this->dashboard->getSummary();
        $valueTour = $this->dashboard->getValueDomain();
        $dataDomain = [
            'values' => [
                $valueTour['b'] ?? 0,
                $valueTour['t'] ?? 0,
                $valueTour['n'] ?? 0,
            ]
        ];

        $paymentStatus = $this->dashboard->getValuePayment();

        $toursBooked = $this->dashboard->getMostTourBooked();
        $newBooking = $this->dashboard->getNewBooking();
        $revenue = $this->dashboard->getRevenuePerMonth();
        $bookingDetails = $this->dashboard->getBookingDetails();
        $revenueByTour = $this->dashboard->getRevenueByTour();
        $scheduleAvailability = $this->dashboard->getScheduleAvailability();
        $vnpayTransactions = $this->dashboard->getVnpayTransactions();

        return view('admin.dashboard', compact(
            'title',
            'summary',
            'dataDomain',
            'paymentStatus',
            'toursBooked',
            'newBooking',
            'revenue',
            'bookingDetails',
            'revenueByTour',
            'scheduleAvailability',
            'vnpayTransactions'
        ));
    }


}
