<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\NotificationController;

Route::get('/', function () {
    $settings = \App\Models\SystemSetting::all()->pluck('value', 'key');
    return view('user', compact('settings'));
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
    Route::post('/admin/login', [App\Http\Controllers\AdminAuthController::class, 'login']);
    Route::post('/admin/logout', [App\Http\Controllers\AdminAuthController::class, 'logout']);
    Route::get('/rooms', [ApiController::class, 'getRooms']);
    Route::post('/rooms/{name}', [ApiController::class, 'updateRoom']);
    
    Route::get('/bookings', [ApiController::class, 'getBookings']);
    Route::post('/bookings', [ApiController::class, 'storeBooking']);
    Route::post('/bookings/{id}', [ApiController::class, 'updateBooking']);
    Route::post('/bookings/{id}/status', [ApiController::class, 'updateBookingStatus']);
    Route::delete('/bookings/{id}', [ApiController::class, 'deleteBooking']);

    // Notification routes
    Route::post('/notify/whatsapp', [NotificationController::class, 'sendWhatsApp']);
    // Admin account CRUD routes (superadmin)
    Route::get('/admin/accounts', [App\Http\Controllers\AdminAccountController::class, 'index']);
    Route::post('/admin/accounts', [App\Http\Controllers\AdminAccountController::class, 'store']);
    Route::put('/admin/accounts/{id}', [App\Http\Controllers\AdminAccountController::class, 'update']);
    Route::delete('/admin/accounts/{id}', [App\Http\Controllers\AdminAccountController::class, 'destroy']);

    // Super admin extra features
    Route::get('/admin/activity-logs', [App\Http\Controllers\ActivityLogController::class, 'index']);
    Route::get('/admin/stats', [App\Http\Controllers\StatsController::class, 'index']);
    Route::get('/admin/reports/csv', [App\Http\Controllers\ReportController::class, 'exportCsv']);
    Route::get('/admin/settings', [App\Http\Controllers\SystemSettingController::class, 'index']);
    Route::post('/admin/settings', [App\Http\Controllers\SystemSettingController::class, 'store']);

    // Super admin — update own profile (username & password)
    Route::put('/admin/profile', [App\Http\Controllers\SuperAdminController::class, 'updateProfile']);
});
