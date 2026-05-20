<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| IMPORT CONTROLLERS
|--------------------------------------------------------------------------
*/

// --- ADMIN CONTROLLERS ---
use App\Http\Controllers\admin\LoginAdminController;
use App\Http\Controllers\admin\DashboardController;
use App\Http\Controllers\admin\AdminManagementController;
use App\Http\Controllers\admin\ToursManagementController;
use App\Http\Controllers\admin\usermanagementController;
use App\Http\Controllers\admin\BookingManagementController;
use App\Http\Controllers\admin\KhuyenMaiController;

// --- CLIENT CONTROLLERS ---
use App\Http\Controllers\clients\LoginController;
use App\Http\Controllers\clients\LoginGoogleController;
use App\Http\Controllers\clients\UserProfileController;
use App\Http\Controllers\clients\ToursController;
use App\Http\Controllers\clients\MyTourController;
use App\Http\Controllers\clients\BookingController;
use App\Http\Controllers\clients\TourBookedController;
use App\Http\Controllers\clients\VnpayPaymentController;


/*
|--------------------------------------------------------------------------
| 1. ADMIN ROUTES
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->group(function () {
    
    // --- Xác thực & Dashboard ---
    Route::get('/login', [LoginAdminController::class, 'index'])->name('admin.login');
    Route::post('/login-account', [LoginAdminController::class, 'loginAdmin'])->name('admin.login-account');
    Route::get('/logout', [LoginAdminController::class, 'logout'])->name('admin.logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    // --- Quản lý Profile Admin ---
    Route::get('/profile', [AdminManagementController::class, 'index'])->name('admin.profile');
    Route::post('/profile/update', [AdminManagementController::class, 'updateAdmin'])->name('admin.update-admin');
// --- Quản lý Người dùng ---//
    Route::get('/users', [usermanagementController::class, 'index'])->name('admin.users');
    Route::post('/users/update-status', [usermanagementController::class, 'changeStatus'])->name('admin.status-user');
    Route::post('/users/unlock', [usermanagementController::class, 'changeStatus'])->name('admin.unlock-user');


        // booking tour 
        Route::get('/booking', [BookingManagementController::class, 'index'])->name('admin.booking');
        Route::get('/booking/{id}/detail', [BookingManagementController::class, 'showDetail'])->name('admin.booking-detail');
    Route::post('/confirm', [BookingManagementController::class, 'confirmBooking'])->name('admin.confirm-booking');
    Route::post('/finish', [BookingManagementController::class, 'finishBooking'])->name('admin.finish-booking');
    Route::post('/received-money', [BookingManagementController::class, 'receiviedMoney'])->name('admin.booking-received-money');
    Route::post('/send-pdf', [BookingManagementController::class, 'sendPdf'])->name('admin.booking-send-pdf');
    Route::post('/refund', [BookingManagementController::class, 'refundMoney'])->name('admin.booking-refund');
    // --- Quản lý Tours ---
    Route::prefix('tours')->group(function () {
        // Danh sách & Xóa
        Route::get('/', [ToursManagementController::class, 'index'])->name('admin.tours');
        Route::post('/delete', [ToursManagementController::class, 'deleteTour'])->name('admin.delete-tour');
        
        // Thêm Tour Mới
       Route::get('/add', [ToursManagementController::class, 'pageAddTours'])->name('admin.page-add-tours');
    Route::post('/add', [ToursManagementController::class, 'addTours'])->name('admin.add-tours');
    Route::post('/upload-image', [ToursManagementController::class, 'addImagesTours'])->name('admin.upload-image-tour');
    Route::post('/add-timeline', [ToursManagementController::class, 'addTimeline'])->name('admin.add-timeline');
        
        // Chỉnh sửa Tour
        Route::get('/edit', [ToursManagementController::class, 'editTour'])->name('admin.tour-edit');
        Route::get('/{tourId}/edit-page', [ToursManagementController::class, 'editPage'])->name('admin.tour-edit-page');
        Route::post('/{tourId}/update', [ToursManagementController::class, 'updateTour'])->name('admin.tour-update');

        // Quản lý Hình ảnh Tour
        Route::post('/{tourId}/images', [ToursManagementController::class, 'addImagesTours'])->name('admin.tour-add-image');
        Route::post('/{tourId}/images/{hinhId}/delete', [ToursManagementController::class, 'deleteImage'])->name('admin.tour-delete-image');
        
        // Quản lý Lịch khởi hành Tour
        Route::post('/{tourId}/schedules', [ToursManagementController::class, 'addSchedule'])->name('admin.tour-add-schedule');
        Route::post('/{tourId}/schedules/{lichId}/status', [ToursManagementController::class, 'updateScheduleStatus'])->name('admin.tour-update-schedule-status');
        Route::post('/{tourId}/schedules/{lichId}/delete', [ToursManagementController::class, 'deleteSchedule'])->name('admin.tour-delete-schedule');

        // (Chú ý: Các route này đang bị trùng lặp chức năng với các route có {tourId} ở trên, mình gom lại đây cho bạn dễ kiểm tra)
        Route::post('/add-image', [ToursManagementController::class, 'addImagesTours'])->name('admin.add-image');
        Route::post('/add-schedule', [ToursManagementController::class, 'addSchedule'])->name('admin.add-schedule');
        Route::post('/delete-image', [ToursManagementController::class, 'deleteImage'])->name('admin.delete-image');
        Route::post('/delete-schedule', [ToursManagementController::class, 'deleteSchedule'])->name('admin.delete-schedule');
   

        });
        Route::prefix('khuyenmai')->group(function () {
            Route::get('/', [KhuyenMaiController::class, 'index'])->name('admin.khuyenmai');
            Route::post('/store', [KhuyenMaiController::class, 'store'])->name('admin.khuyenmai.store');
            Route::post('/toggle-status', [KhuyenMaiController::class, 'toggleStatus'])->name('admin.khuyenmai.toggle');
            Route::post('/delete', [KhuyenMaiController::class, 'destroy'])->name('admin.khuyenmai.delete');
        });
});


/*
|--------------------------------------------------------------------------
| 2. CLIENT ROUTES
|--------------------------------------------------------------------------
*/

// --- Các trang chung (Public Pages) ---
Route::get('/', function () {
    return view('clients.home', ['title' => 'Trang chủ', 'tours' => collect(), 'toursPopular' => collect()]);
})->name('home');

Route::get('/about', fn () => view('clients.about', ['title' => 'Giới thiệu']))->name('about');
Route::get('/team', fn () => redirect()->route('home'))->name('team');
Route::get('/contact', fn () => redirect()->route('home'))->name('contact');
Route::get('/search', fn () => redirect()->route('home'))->name('search');
Route::get('/search-voice-text', fn () => redirect()->route('home'))->name('search-voice-text');

// --- Xác thực người dùng (Auth) ---
Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('user-login');
Route::post('/register', [LoginController::class, 'register'])->name('register');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

// Đăng nhập bằng Google
Route::get('auth/google', [LoginGoogleController::class, 'redirectToGoogle'])->name('login-google');
Route::get('auth/google/callback', [LoginGoogleController::class, 'handleGoogleCallback']);

// --- Quản lý Tài khoản (User Profile) ---
Route::get('/user-profile', [UserProfileController::class, 'index'])->name('user-profile');
Route::post('/user-profile', [UserProfileController::class, 'update'])->name('update-user-profile');
Route::post('/change-password-profile', [UserProfileController::class, 'changePassword'])->name('change-password');

// --- Tour (Danh sách & Chi tiết) ---
Route::get('/tours', [ToursController::class, 'index'])->name('tours');
Route::get('/filter-tours', [ToursController::class, 'filterTours'])->name('filter-tours');
Route::get('/tour-detail/{id}', [ToursController::class, 'show'])->name('tour-detail');
Route::get('/destination', [ToursController::class, 'destination'])->name('destination');

// --- Booking & Lịch sử Đặt Tour ---
Route::get('/my-tours', [MyTourController::class, 'index'])->name('my-tours');
Route::get('/booking/{id}', [BookingController::class, 'index'])->name('booking-form');
Route::post('/booking/{id}', [BookingController::class, 'createBooking'])->name('booking');
Route::get('/tour-booked/{id}', [BookingController::class, 'showBooked'])->name('tour-booked');
Route::post('/cancel-booking', [TourBookedController::class, 'cancelBooking'])->name('cancelBooking'); // Đã xóa dòng duplicate ở bản gốc
Route::post('/check-promo', [BookingController::class, 'checkPromo'])->name('check-promo');

// --- Thanh toán (Payments) ---
Route::post('/createMomoPayment', [BookingController::class, 'createMomoPayment'])->name('createMomoPayment');
Route::get('/momo-callback', [BookingController::class, 'momoCallback'])->name('momo.callback');

Route::match(['get', 'post'], '/vnpay-payment', [VnpayPaymentController::class, 'payment'])->name('vnpay.payment');
Route::get('/vnpay-callback', [VnpayPaymentController::class, 'callback'])->name('vnpay.callback');

// --- Đánh giá (Reviews) ---
Route::post('/reviews', [ToursController::class, 'storeReview'])->name('reviews');
Route::post('/checkBooking', [BookingController::class, 'checkBooking'])->name('checkBooking');


/*
|--------------------------------------------------------------------------
| 3. ASSETS ROUTES (Nên cấu hình qua Nginx/Apache nếu deploy thực tế)
|--------------------------------------------------------------------------
*/
Route::get('/clients/assets/{path}', function (string $path) {
    $basePath = realpath(public_path('clients/assets'));
    $fullPath = realpath($basePath . '\\' . str_replace('/', '\\', $path));

    if (!$basePath || !$fullPath || !str_starts_with(strtolower($fullPath), strtolower($basePath)) || !is_file($fullPath)) {
        abort(404);
    }
    return response()->file($fullPath);
})->where('path', '.*');

Route::get('/admin/assets/{path}', function (string $path) {
    $basePath = realpath(public_path('admin/assets'));
    $fullPath = realpath($basePath . '\\' . str_replace('/', '\\', $path));

    if (!$basePath || !$fullPath || !str_starts_with(strtolower($fullPath), strtolower($basePath)) || !is_file($fullPath)) {
        abort(404);
    }
    return response()->file($fullPath);
})->where('path', '.*');
