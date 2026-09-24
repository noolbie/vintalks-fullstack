<?php

use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\ConsultationResultController as AdminConsultationResultController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\MentorController as AdminMentorController;
use App\Http\Controllers\Admin\PackageApprovalController as AdminPackageApprovalController;
use App\Http\Controllers\Admin\PackageController as AdminPackageController;
use App\Http\Controllers\Admin\ParticipantController as AdminParticipantController;
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\Admin\SettingsController as AdminSettingsController;
use App\Http\Controllers\Admin\TopicController;
use App\Http\Controllers\Admin\TransactionController as AdminTransactionController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ConsultationResultDocumentController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Mentor\AvailabilityController as MentorAvailabilityController;
use App\Http\Controllers\Mentor\BookingController as MentorBookingController;
use App\Http\Controllers\Mentor\ConsultationResultController as MentorConsultationResultController;
use App\Http\Controllers\Mentor\DashboardController as MentorDashboardController;
use App\Http\Controllers\Mentor\EarningsController as MentorEarningsController;
use App\Http\Controllers\Mentor\ProfileController as MentorProfileController;
use App\Http\Controllers\Participant\BookingController as ParticipantBookingController;
use App\Http\Controllers\Participant\ConsultationResultController as ParticipantConsultationResultController;
use App\Http\Controllers\Participant\DashboardController as ParticipantDashboardController;
use App\Http\Controllers\Participant\DocumentController as ParticipantDocumentController;
use App\Http\Controllers\Participant\MentorController as ParticipantMentorController;
use App\Http\Controllers\Participant\PaymentController as ParticipantPaymentController;
use App\Http\Controllers\Participant\ProfileController as ParticipantProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public routes
|--------------------------------------------------------------------------
*/
// Rute publik: halaman utama + login/register untuk pengunjung yang belum masuk.
Route::get('/', [HomeController::class, 'index'])->name('home');
// Pilih paket dari landing page (simpan di session, arahkan ke register/daftar mentor).
Route::get('paket/{package}/pilih', [HomeController::class, 'selectPackage'])->name('packages.select');

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.attempt');
    Route::get('register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('register', [AuthController::class, 'register'])->name('register.attempt');
});

Route::post('logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

/*
|--------------------------------------------------------------------------
| Private signed document downloads
|--------------------------------------------------------------------------
*/
// Rute khusus unduhan dokumen privat lewat URL signed yang hanya berlaku sementara (30 menit).
Route::middleware('signed')->group(function () {
    Route::get('documents/{document}', [DocumentController::class, 'show'])->name('documents.show');
    Route::get('consultation-results/documents/{document}', [ConsultationResultDocumentController::class, 'show'])
        ->name('consultation-results.documents.show');
});

/*
|--------------------------------------------------------------------------
| Participant
|--------------------------------------------------------------------------
*/
// Grup rute khusus role participant: dashboard, profil, cari mentor, booking, pembayaran, dokumen, notifikasi.
Route::middleware(['auth', 'role:participant'])->prefix('participant')->name('participant.')->group(function () {
    Route::get('dashboard', [ParticipantDashboardController::class, 'index'])->name('dashboard');

    Route::get('profile', [ParticipantProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [ParticipantProfileController::class, 'update'])->name('profile.update');

    Route::get('mentors', [ParticipantMentorController::class, 'index'])->name('mentors.index');
    Route::get('mentors/{mentor}', [ParticipantMentorController::class, 'show'])->name('mentors.show');
    Route::get('mentors/{mentor}/slots', [ParticipantMentorController::class, 'slots'])->name('mentors.slots');

    Route::get('mentors/{mentor}/bookings/create', [ParticipantBookingController::class, 'create'])->name('bookings.create');
    Route::post('mentors/{mentor}/bookings', [ParticipantBookingController::class, 'store'])->name('mentors.bookings.store');

    Route::get('bookings', [ParticipantBookingController::class, 'history'])->name('bookings.history');
    Route::get('bookings/{booking}', [ParticipantBookingController::class, 'show'])->name('bookings.show');
    Route::get('bookings/{booking}/cancel', [ParticipantBookingController::class, 'cancelReason'])->name('bookings.cancel-reason');
    Route::post('bookings/{booking}/cancel', [ParticipantBookingController::class, 'cancel'])->name('bookings.cancel');

    Route::post('bookings/{booking}/documents', [ParticipantDocumentController::class, 'store'])->name('bookings.documents.store');
    Route::delete('bookings/{booking}/documents/{document}', [ParticipantDocumentController::class, 'destroy'])->name('bookings.documents.destroy');

    Route::get('bookings/{booking}/payments/create', [ParticipantPaymentController::class, 'create'])->name('payments.create');
    Route::post('bookings/{booking}/payments', [ParticipantPaymentController::class, 'store'])->name('payments.store');

    Route::get('consultation-results/{result}', [ParticipantConsultationResultController::class, 'show'])->name('consultation-results.show');

    Route::get('notifications', [ParticipantDashboardController::class, 'notifications'])->name('notifications');
    Route::post('notifications/{id}/read', [ParticipantDashboardController::class, 'markNotificationRead'])->name('notifications.read');
});

/*
|--------------------------------------------------------------------------
| Mentor
|--------------------------------------------------------------------------
*/
// Grup rute khusus role mentor: dashboard, profil, jadwal ketersediaan, booking, dan hasil konsultasi.
Route::middleware(['auth', 'role:mentor'])->prefix('mentor')->name('mentor.')->group(function () {
    Route::get('dashboard', [MentorDashboardController::class, 'index'])->name('dashboard');

    Route::get('profile', [MentorProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [MentorProfileController::class, 'update'])->name('profile.update');

    Route::get('availability', [MentorAvailabilityController::class, 'index'])->name('availability.index');
    Route::post('availability', [MentorAvailabilityController::class, 'store'])->name('availability.store');
    Route::delete('availability/{availability}', [MentorAvailabilityController::class, 'destroy'])->name('availability.destroy');

    Route::get('bookings', [MentorBookingController::class, 'index'])->name('bookings.index');
    Route::get('bookings/{booking}', [MentorBookingController::class, 'show'])->name('bookings.show');
    Route::post('bookings/{booking}/complete', [MentorBookingController::class, 'complete'])->name('bookings.complete');

    Route::get('earnings', [MentorEarningsController::class, 'index'])->name('earnings.index');

    Route::get('bookings/{booking}/consultation-results/create', [MentorConsultationResultController::class, 'create'])->name('consultation-results.create');
    Route::post('bookings/{booking}/consultation-results', [MentorConsultationResultController::class, 'store'])->name('consultation-results.store');
});

/*
|--------------------------------------------------------------------------
| Admin
|--------------------------------------------------------------------------
*/
// Grup rute khusus role admin: dashboard, pengguna, peserta, mentor, topik, booking, pembayaran, hasil, dan pengaturan.
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::get('users', [AdminUserController::class, 'index'])->name('users.index');
    Route::delete('users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');

    Route::get('participants', [AdminParticipantController::class, 'index'])->name('participants.index');
    Route::get('participants/{user}', [AdminParticipantController::class, 'show'])->name('participants.show');

    Route::get('mentors', [AdminMentorController::class, 'index'])->name('mentors.index');
    Route::get('mentors/create', [AdminMentorController::class, 'create'])->name('mentors.create');
    Route::post('mentors', [AdminMentorController::class, 'store'])->name('mentors.store');
    Route::get('mentors/{mentor}', [AdminMentorController::class, 'show'])->name('mentors.show');
    Route::post('mentors/{mentor}/toggle-active', [AdminMentorController::class, 'toggleActive'])->name('mentors.toggle-active');

    Route::get('topics', [TopicController::class, 'index'])->name('topics.index');
    Route::post('topics', [TopicController::class, 'store'])->name('topics.store');
    Route::put('topics/{topic}', [TopicController::class, 'update'])->name('topics.update');
    Route::delete('topics/{topic}', [TopicController::class, 'destroy'])->name('topics.destroy');

    Route::get('packages', [AdminPackageController::class, 'index'])->name('packages.index');
    Route::get('packages/create', [AdminPackageController::class, 'create'])->name('packages.create');
    Route::post('packages', [AdminPackageController::class, 'store'])->name('packages.store');
    Route::get('packages/{package}/edit', [AdminPackageController::class, 'edit'])->name('packages.edit');
    Route::put('packages/{package}', [AdminPackageController::class, 'update'])->name('packages.update');
    Route::delete('packages/{package}', [AdminPackageController::class, 'destroy'])->name('packages.destroy');

    Route::get('package-approvals', [AdminPackageApprovalController::class, 'index'])->name('package-approvals.index');
    Route::post('package-approvals/{booking}/approve', [AdminPackageApprovalController::class, 'approve'])->name('package-approvals.approve');
    Route::post('package-approvals/{booking}/reject', [AdminPackageApprovalController::class, 'reject'])->name('package-approvals.reject');

    Route::get('bookings', [AdminBookingController::class, 'index'])->name('bookings.index');
    Route::get('bookings/{booking}', [AdminBookingController::class, 'show'])->name('bookings.show');
    Route::post('bookings/{booking}/meeting', [AdminBookingController::class, 'updateMeeting'])->name('bookings.meeting');
    Route::post('bookings/{booking}/cancel', [AdminBookingController::class, 'cancel'])->name('bookings.cancel');

    Route::get('payments', [AdminPaymentController::class, 'index'])->name('payments.index');
    Route::get('payments/{payment}', [AdminPaymentController::class, 'show'])->name('payments.show');
    Route::post('payments/{payment}/verify', [AdminPaymentController::class, 'verify'])->name('payments.verify');
    Route::post('payments/{payment}/reject', [AdminPaymentController::class, 'reject'])->name('payments.reject');

    Route::get('transactions', [AdminTransactionController::class, 'index'])->name('transactions.index');
    Route::post('transactions/{booking}/mark-paid', [AdminTransactionController::class, 'markPaid'])->name('transactions.mark-paid');

    Route::get('consultation-results', [AdminConsultationResultController::class, 'index'])->name('consultation-results.index');
    Route::get('consultation-results/{result}', [AdminConsultationResultController::class, 'show'])->name('consultation-results.show');

    Route::get('settings', [AdminSettingsController::class, 'edit'])->name('settings.edit');
    Route::put('settings', [AdminSettingsController::class, 'update'])->name('settings.update');
});