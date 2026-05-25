<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\NotificationController;

Route::get('/', function () {
    return view('user');
});

use Illuminate\Support\Facades\Auth;

Route::get('/admin', function () {
    return view('admin_panel', [
        'adminRole' => Auth::check() ? Auth::user()->role : 'admin', 
        'adminUser' => Auth::user()
    ]);
});

// API Routes for JS frontend
Route::prefix('api')->group(function() {
    Route::post('/admin/login', [\App\Http\Controllers\AdminAuthController::class, 'login']);
    Route::post('/admin/logout', [\App\Http\Controllers\AdminAuthController::class, 'logout']);
    Route::get('/rooms', [ApiController::class, 'getRooms']);
    Route::post('/rooms/{name}', [ApiController::class, 'updateRoom']);
    
    Route::get('/bookings', [ApiController::class, 'getBookings']);
    Route::post('/bookings', [ApiController::class, 'storeBooking']);
    Route::post('/bookings/{id}', [ApiController::class, 'updateBooking']);
    Route::post('/bookings/{id}/status', [ApiController::class, 'updateBookingStatus']);
    Route::delete('/bookings/{id}', [ApiController::class, 'deleteBooking']);

    // Notification routes
    Route::post('/notify/whatsapp', [NotificationController::class, 'sendWhatsApp']);
    Route::post('/admins', [App\\Http\\Controllers\\SuperAdminController::class, 'store']);
});
