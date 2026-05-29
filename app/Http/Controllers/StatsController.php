<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Room;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StatsController extends Controller
{
    /**
     * Get statistics for Super Admin dashboard.
     */
    public function index(): JsonResponse
    {
        if (auth()->check() && auth()->user()->role !== 'super_admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        // 1. Monthly booking trends (last 6 months)
        $monthlyTrends = Booking::select(
            DB::raw("DATE_FORMAT(tanggal, '%Y-%m') as month"),
            DB::raw("COUNT(*) as total"),
            DB::raw("SUM(case when status = 'disetujui' then 1 else 0 end) as approved"),
            DB::raw("SUM(case when status = 'ditolak' then 1 else 0 end) as rejected")
        )
        ->groupBy('month')
        ->orderBy('month', 'desc')
        ->limit(6)
        ->get()
        ->reverse()
        ->values();

        // 2. Breakdown per room
        $roomBreakdown = Room::select('rooms.name as name')
            ->selectRaw('COUNT(bookings.id) as total')
            ->selectRaw("SUM(case when bookings.status = 'disetujui' then 1 else 0 end) as approved")
            ->leftJoin('bookings', 'bookings.room_id', '=', 'rooms.id')
            ->groupBy('rooms.id', 'rooms.name')
            ->get();

        // 3. Breakdown per instansi (top 5)
        $instansiBreakdown = Booking::select('instansi')
            ->selectRaw('COUNT(*) as total')
            ->whereNotNull('instansi')
            ->where('instansi', '<>', '')
            ->groupBy('instansi')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();

        // 4. Admin approval rates
        // Get all admins
        $admins = User::where('role', 'admin')->orWhere('role', 'super_admin')->get(['id', 'name', 'username']);
        $adminRates = [];

        foreach ($admins as $admin) {
            $approvals = ActivityLog::where('user_id', $admin->id)->where('action', 'approve_booking')->count();
            $rejections = ActivityLog::where('user_id', $admin->id)->where('action', 'reject_booking')->count();
            $total = $approvals + $rejections;
            
            $adminRates[] = [
                'id' => $admin->id,
                'name' => $admin->name,
                'username' => $admin->username,
                'approvals' => $approvals,
                'rejections' => $rejections,
                'total_actions' => $total,
                'rate' => $total > 0 ? round(($approvals / $total) * 100, 1) : 100
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'monthlyTrends' => $monthlyTrends,
                'roomBreakdown' => $roomBreakdown,
                'instansiBreakdown' => $instansiBreakdown,
                'adminRates' => $adminRates
            ]
        ]);
    }
}
