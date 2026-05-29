<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Response;

class ReportController extends Controller
{
    /**
     * Export bookings to CSV.
     */
    public function exportCsv(Request $request)
    {
        if (auth()->check() && auth()->user()->role !== 'super_admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $query = Booking::with('room');

        // Apply filters
        if ($request->has('start_date') && !empty($request->start_date)) {
            $query->whereDate('tanggal', '>=', $request->start_date);
        }
        if ($request->has('end_date') && !empty($request->end_date)) {
            $query->whereDate('tanggal', '<=', $request->end_date);
        }
        if ($request->has('room_id') && !empty($request->room_id)) {
            $query->where('room_id', $request->room_id);
        }
        if ($request->has('status') && !empty($request->status)) {
            $dbStatus = $request->status == 'approved' ? 'disetujui' : ($request->status == 'rejected' ? 'ditolak' : 'pending');
            $query->where('status', $dbStatus);
        }

        $bookings = $query->orderBy('tanggal', 'asc')->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="Laporan_Reservasi_' . date('Ymd_His') . '.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function() use ($bookings) {
            $file = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for proper Excel compatibility
            fputs($file, "\xEF\xBB\xBF");
            
            // CSV headers
            fputcsv($file, [
                'ID Reservasi', 
                'Nama Pemohon', 
                'Instansi', 
                'No. Kontak', 
                'Ruangan', 
                'Tanggal', 
                'Jam Mulai', 
                'Jam Selesai', 
                'Keperluan', 
                'Status', 
                'Catatan/Alasan Penolakan',
                'Tanggal Diajukan'
            ]);

            foreach ($bookings as $b) {
                $statusText = $b->status == 'disetujui' ? 'Disetujui' : ($b->status == 'ditolak' ? 'Ditolak' : 'Menunggu');
                fputcsv($file, [
                    $b->id,
                    $b->nama,
                    $b->instansi ?: '-',
                    $b->kontak,
                    $b->room ? $b->room->nama_ruang : 'Unknown',
                    $b->tanggal,
                    $b->jam_mulai,
                    $b->jam_selesai,
                    $b->keperluan,
                    $statusText,
                    $b->alasan_penolakan ?: '-',
                    $b->created_at->format('Y-m-d H:i:s')
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
