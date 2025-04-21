<?php

use App\Exports\ContactsExport;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminPageController;
use App\Http\Controllers\Admin\AdminRoleController;
use App\Http\Controllers\Frontend\FrontendController;
use App\Http\Controllers\Admin\AdminProfileController;
use App\Http\Controllers\Admin\AdminPermissionController;
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\ContactTypeController;
use Maatwebsite\Excel\Facades\Excel;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['middleware' => 'admin.redirect'], function () {
    Route::get('/admin-login', [AdminAuthController::class, 'showLoginPage'])->name('admin.login.page');
    Route::post('/admin-login', [AdminAuthController::class, 'Login'])->name('admin.login');
    Route::get('/student-register', [AdminAuthController::class, 'showRegisterPage'])->name('student.register.page');
    Route::post('/student-register', [AdminAuthController::class, 'register'])->name('student.register');
    Route::get('/forget-password', [AdminProfileController::class, 'ShowForgetPasswordPage'])->name('forget.password.page');
    Route::post('/forget-password', [AdminProfileController::class, 'ForgetPassword'])->name('forget.password');
    Route::get('/reset-password/{token?}/{email?}', [AdminProfileController::class, 'ResetPasswordLink'])->name('reset.password.page');
    Route::post('/reset-password/', [AdminProfileController::class, 'ResetPassword'])->name('reset.password');
});

Route::group(['middleware' => 'admin'], function () {
    Route::get('/dashboard', [AdminPageController::class, 'showDashboardPage'])->name('admin.dashboard.page');
    Route::get('/profile', [AdminPageController::class, 'showProfilePage'])->name('admin.profile.page');
    Route::post('/profile', [AdminPageController::class, 'updateProfile'])->name('admin.profile.update');
    Route::post('/profile-password', [AdminPageController::class, 'updatePassword'])->name('admin.password.update');
    Route::get('/admin-logout', [AdminAuthController::class, 'Logout'])->name('admin.logout.page');
    Route::resource('/permission', AdminPermissionController::class);
    Route::resource('/role', AdminRoleController::class);
    Route::resource('/admin-user', AdminController::class);
    Route::get('contact-trash', [ContactController::class, 'trashUsers'])->name('contact.trash');  // Trash page
    Route::get('contact-trash/{id}', [ContactController::class, 'updateTrash'])->name('contact.trash.update');  // Trash page
    Route::get('/admin-user-status-update/{id}', [AdminController::class, 'updateStatus'])->name('admin.status.update');
    Route::get('/admin-user-trash-update/{id}', [AdminController::class, 'updateTrash'])->name('admin.trash.update');
    Route::get('/admin-trash', [AdminController::class, 'trashUsers'])->name('admin.trash');

    Route::get('contacts-export', function () {
        return Excel::download(new ContactsExport(), 'all_contacts.xlsx');
    })->name('contacts.export.all');

    Route::get('contacts-export/{contactTypeId}', function ($contactTypeId) {
        $searchQuery = request()->get('search'); // Corrected here
        return Excel::download(new ContactsExport($contactTypeId, $searchQuery), (new ContactsExport($contactTypeId))->fileName());
    })->name('contacts.export.type');
    Route::get('/contact/{id}/logs', [ContactController::class, 'logs'])->name('contact.logs');
    // Route for printing contact details
    Route::get('/contact/{id}/print', [ContactController::class, 'printContact'])->name('contact.print');
});
Route::group(['middleware' => 'route.redirect'], function () {
    Route::resource('/permission', AdminPermissionController::class);
    Route::resource('/role', AdminRoleController::class);
    Route::resource('/admin-user', AdminController::class);
    Route::resource('contact', ContactController::class);
    Route::resource('contact-type', ContactTypeController::class);
    Route::post('/contact-type/ajax', [ContactTypeController::class, 'storeAjax'])->name('contact-type-ajax');
    Route::get('/admin-user/last/seen', [AdminController::class, 'getLastSeen'])->name('admin-user.last-seen');
});


Route::get('/', [FrontendController::class, 'showHomePage'])->name('home.page');
