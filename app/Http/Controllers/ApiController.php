<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Room;
use App\Models\Booking;
use Carbon\Carbon;
use App\Models\ActivityLog;

class ApiController extends Controller
{
    public function getRooms()
    {
        $rooms = Room::all();
        // Return in format expected by JS
        $data = $rooms->map(function ($room) {
            return [
                'id' => $room->id,
                'name' => $room->name,
                'nama_ruang' => $room->nama_ruang,
                'cap' => $room->cap,
                'fasilitas' => $room->fasilitas,
                'status' => $room->status,
                'closedUntil' => $room->closedUntil
            ];
        });
        return response()->json($data);
    }

    public function getBookings()
    {
        $bookings = Booking::with('room')->get();
        $data = $bookings->map(function ($b) {
            return [
                'id' => $b->id,
                'ruangan_id' => $b->room_id,
                'ruangan' => $b->room ? $b->room->nama_ruang : 'Unknown',
                'nama' => $b->nama,
                'instansi' => $b->instansi,
                'kontak' => $b->kontak,
                'tanggal' => $b->tanggal,
                'jamMulai' => substr($b->jam_mulai, 0, 5),
                'jamSelesai' => substr($b->jam_selesai, 0, 5),
                'keperluan' => $b->keperluan,
                'status' => $b->status == 'disetujui' ? 'approved' : ($b->status == 'ditolak' ? 'rejected' : 'pending'),
                'alasan_penolakan' => $b->alasan_penolakan,
                'createdAt' => $b->created_at->toIso8601String()
            ];
        });
        return response()->json($data);
    }

    public function storeBooking(Request $request)
    {
        $request->validate([
            'room_name' => 'required',
            'nama' => 'required',
            'instansi' => 'nullable',
            'kontak' => 'required',
            'tanggal' => 'required|date',
            'jamMulai' => 'required',
            'jamSelesai' => 'required',
            'keperluan' => 'required',
        ]);

        $room = Room::where('name', $request->room_name)->first();
        if (!$room) return response()->json(['success' => false, 'message' => 'Room not found'], 404);

        $booking = Booking::create([
            'room_id' => $room->id,
            'nama' => $request->nama,
            'instansi' => $request->instansi,
            'kontak' => $request->kontak,
            'tanggal' => $request->tanggal,
            'jam_mulai' => $request->jamMulai,
            'jam_selesai' => $request->jamSelesai,
            'keperluan' => $request->keperluan,
            'status' => 'pending'
        ]);

        return response()->json(['success' => true, 'booking_id' => $booking->id]);
    }

    public function updateBookingStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected,pending',
            'alasan_penolakan' => 'nullable|string'
        ]);
        $booking = Booking::findOrFail($id);
        
        $dbStatus = $request->status == 'approved' ? 'disetujui' : ($request->status == 'rejected' ? 'ditolak' : 'pending');
        
        $updateData = ['status' => $dbStatus];
        if ($request->has('alasan_penolakan')) {
            $updateData['alasan_penolakan'] = $request->alasan_penolakan;
        }

        $booking->update($updateData);

        $action = $request->status === 'approved' ? 'approve_booking' : ($request->status === 'rejected' ? 'reject_booking' : 'reset_booking');
        $statusLabel = $request->status === 'approved' ? 'menyetujui' : ($request->status === 'rejected' ? 'menolak' : 'mereset');
        $roomName = $booking->room->nama_ruang ?? 'Unknown';
        $details = "Admin " . (auth('web')->user()->name ?? 'System') . " {$statusLabel} pemesanan #{$booking->id} oleh {$booking->nama} (Ruangan: {$roomName})." . ($request->alasan_penolakan ? " Catatan: \"{$request->alasan_penolakan}\"" : "");

        ActivityLog::create([
            'user_id' => auth('web')->id(),
            'action' => $action,
            'model_type' => 'Booking',
            'model_id' => $booking->id,
            'details' => $details,
            'ip_address' => $request->ip()
        ]);

        return response()->json(['success' => true]);
    }

    public function deleteBooking($id)
    {
        $booking = Booking::findOrFail($id);
        $booking->delete();

        $roomName = $booking->room->nama_ruang ?? 'Unknown';
        $details = "Admin " . (auth('web')->user()->name ?? 'System') . " menghapus pemesanan #{$booking->id} oleh {$booking->nama} (Ruangan: {$roomName}).";
        ActivityLog::create([
            'user_id' => auth('web')->id(),
            'action' => 'delete_booking',
            'model_type' => 'Booking',
            'model_id' => $booking->id,
            'details' => $details,
            'ip_address' => request()->ip()
        ]);

        return response()->json(['success' => true]);
    }

    public function updateRoom(Request $request, $name)
    {
        $room = Room::where('name', $name)->firstOrFail();
        
        if ($request->has('cap')) $room->cap = $request->cap;
        if ($request->has('fasilitas')) $room->fasilitas = $request->fasilitas;
        if ($request->has('status')) {
            $room->status = $request->status;
        }
        if ($request->has('closedUntil')) $room->closedUntil = $request->closedUntil ?: null;

        $room->save();

        $details = "Admin " . (auth('web')->user()->name ?? 'System') . " mengubah data ruangan {$room->name}. Status: {$room->status}, Kapasitas: {$room->cap} orang.";
        ActivityLog::create([
            'user_id' => auth('web')->id(),
            'action' => 'update_room',
            'model_type' => 'Room',
            'model_id' => $room->id,
            'details' => $details,
            'ip_address' => $request->ip()
        ]);

        return response()->json(['success' => true]);
    }

    public function updateBooking(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);
        
        if ($request->has('nama')) $booking->nama = $request->nama;
        if ($request->has('instansi')) $booking->instansi = $request->instansi;
        if ($request->has('tanggal')) $booking->tanggal = $request->tanggal;
        if ($request->has('jamMulai')) $booking->jam_mulai = $request->jamMulai;
        if ($request->has('jamSelesai')) $booking->jam_selesai = $request->jamSelesai;
        if ($request->has('keperluan')) $booking->keperluan = $request->keperluan;
        
        if ($request->has('ruangan')) {
            $room = Room::where('name', $request->ruangan)->first();
            if ($room) {
                $booking->room_id = $room->id;
            }
        }

        $booking->save();

        $details = "Admin " . (auth('web')->user()->name ?? 'System') . " mengubah rincian pemesanan #{$booking->id} oleh {$booking->nama}.";
        ActivityLog::create([
            'user_id' => auth('web')->id(),
            'action' => 'update_booking',
            'model_type' => 'Booking',
            'model_id' => $booking->id,
            'details' => $details,
            'ip_address' => $request->ip()
        ]);

        return response()->json(['success' => true]);
    }
}
