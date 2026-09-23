<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentProofRequest;
use App\Models\Booking;
use App\Models\Setting;
use App\Services\PaymentService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PaymentController extends Controller
{
    // ==== Pembayaran peserta ====

    // Tampilkan halaman pembayaran: mode upload bukti, Google Form, atau instruksi transfer bank.
    public function create(Booking $booking): View
    {
        abort_unless($booking->isOwnedByParticipant(Auth::id()), 403);

        abort_if($booking->paymentIsVerified(), 403, 'Pembayaran sudah terverifikasi.');

        $booking->load(['mentorProfile', 'payment']);

        $paymentMode = config('vintalks.payment_mode', 'upload');
        $googleFormUrl = config('vintalks.google_form_url');

        return view('participant.payments.create', compact('booking', 'paymentMode', 'googleFormUrl') + [
            'paymentMethods' => Setting::paymentMethods(),
            'account' => [
                'bank_name' => Setting::get('payment_bank_name', ''),
                'account_name' => Setting::get('payment_account_name', ''),
                'account_number' => Setting::get('payment_account_number', ''),
            ],
        ]);
    }

    // Terima bukti bayar peserta, simpan pembayaran, dan ubah status booking jadi menunggu verifikasi admin.
    public function store(PaymentProofRequest $request, Booking $booking, PaymentService $paymentService): \Illuminate\Http\RedirectResponse
    {
        abort_unless($booking->isOwnedByParticipant(Auth::id()), 403);

        try {
            $paymentService->submitProof($booking, $request->file('proof'), $request->only(['payment_method', 'payment_reference']));
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors());
        }

        return redirect()->route('participant.bookings.show', $booking)
            ->with('success', 'Bukti pembayaran berhasil dikirim. Menunggu verifikasi admin.');
    }
}