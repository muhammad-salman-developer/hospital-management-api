<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\Review;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function stats()
    {
        $totalPatients = Patient::count();
        $totalDoctors = Doctor::count();
        $totalAppointments = Appointment::count();
        $pendingAppointments = Appointment::where('status', 'pending')->count();
        $totalRevenue = Payment::where('status', 'paid')->sum('amount');
        $averageRating = Review::avg('rating');

        return response()->json([
            'status' => true,
            'data' => [
                'total_patients' => $totalPatients,
                'total_doctors' => $totalDoctors,
                'total_appointments' => $totalAppointments,
                'pending_appointments' => $pendingAppointments,
                'total_revenue' => round($totalRevenue, 2),
                'average_rating' => $averageRating ? round($averageRating, 1) : 0,
            ],
        ], 200);
    }

    public function appointmentsTrend()
    {
        $days = 7;
        $data = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);

            $count = Appointment::whereDate('appointment_date', $date)->count();

            $data[] = [
                'date' => $date->format('M d'),
                'count' => $count,
            ];
        }

        return response()->json([
            'status' => true,
            'data' => $data,
        ], 200);
    }

    public function revenueTrend()
    {
        $days = 7;
        $data = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);

            $total = Payment::where('status', 'paid')
                ->whereDate('paid_at', $date)
                ->sum('amount');

            $data[] = [
                'date' => $date->format('M d'),
                'revenue' => round($total, 2),
            ];
        }

        return response()->json([
            'status' => true,
            'data' => $data,
        ], 200);
    }

    public function todaysAppointments()
    {
        $appointments = Appointment::with(['doctor.user', 'patient'])
            ->whereDate('appointment_date', Carbon::today())
            ->orderBy('appointment_time')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $appointments,
        ], 200);
    }
}