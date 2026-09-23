<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentRejectRequest;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    // ==== Manajemen pembayaran ====

    // Daftar pembayaran dengan filter status & pencarian kode booking.
    public function index(Request $request): View
    {
        $payments = Payment::with(['booking.participant', 'booking.mentorProfile', 'proofDocument'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->input('status')))
            ->when($request->filled('q'), fn ($q) => $q->whereHas('booking', fn ($b) => $b->where('booking_code', 'like', '%'.$request->string('q').'%')))
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.payments.index', compact('payments'));
    }

    // Detail pembayaran beserta booking, peserta, mentor, syarat, dan bukti bayar.
    public function show(Payment $payment): View
    {
        $payment->load(['booking.participant.participantProfile', 'booking.mentorProfile', 'booking.requirement', 'proofDocument']);

        return view('admin.payments.show', compact('payment'));
    }

    // Verifikasi pembayaran lalu konfirmasi booking (via PaymentService).
    public function verify(Request $request, Payment $payment, PaymentService $paymentService): \Illuminate\Http\RedirectResponse
    {
        try {
            $paymentService->verify($payment, $request->user());
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors());
        }

        return back()->with('success', 'Pembayaran diverifikasi dan booking dikonfirmasi.');
    }

    // Tolak pembayaran dengan alasan dan beri tahu peserta.
    public function reject(PaymentRejectRequest $request, Payment $payment, PaymentService $paymentService): \Illuminate\Http\RedirectResponse
    {
        $paymentService->reject($payment, $request->user(), $request->input('rejection_reason'));

        return back()->with('success', 'Pembayaran ditolak. Peserta telah diberitahu.');
    }
}