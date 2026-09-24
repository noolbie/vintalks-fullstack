<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PackageApprovalStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PackageApprovalController extends Controller
{
    // ==== Persetujuan paket yang dipilih peserta ====

    // Daftar booking yang memakai paket opsional filter status persetujuan.
    public function index(Request $request): View
    {
        $bookings = Booking::query()
            ->with(['package', 'participant', 'mentorProfile'])
            ->whereNotNull('package_id')
            ->when($request->filled('status'), fn ($q) => $q->where('package_approval_status', $request->input('status')))
            ->when(! $request->filled('status'), fn ($q) => $q->where('package_approval_status', PackageApprovalStatus::Pending->value))
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.package-approvals.index', compact('bookings'));
    }

    // Setujui paket: diskon final dan mentor bisa melihat hasilnya.
    public function approve(Booking $booking): RedirectResponse
    {
        if (! $booking->hasPackage() || ! $booking->isPackagePending()) {
            throw ValidationException::withMessages([
                'package' => 'Paket ini tidak sedang menunggu persetujuan.',
            ]);
        }

        $booking->forceFill([
            'package_approval_status' => PackageApprovalStatus::Approved->value,
            'package_approved_at' => now(),
            'package_rejected_at' => null,
            'package_rejection_reason' => null,
        ])->save();

        return back()->with('success', 'Paket disetujui. Mentor dapat melihat potongan & benefit untuk sesi ini.');
    }

    // Tolak paket: harga sesi dikembalikan ke harga normal mentor dan catat alasan.
    public function reject(Request $request, Booking $booking): RedirectResponse
    {
        $request->validate([
            'reason' => ['required', 'string', 'max:1000'],
        ]);

        if (! $booking->hasPackage() || ! $booking->isPackagePending()) {
            throw ValidationException::withMessages([
                'package' => 'Paket ini tidak sedang menunggu persetujuan.',
            ]);
        }

        $booking->forceFill([
            'package_approval_status' => PackageApprovalStatus::Rejected->value,
            'package_rejected_at' => now(),
            'package_rejection_reason' => $request->input('reason'),
            'price' => $booking->mentorProfile->price,
            'discount_amount' => null,
        ])->save();

        return back()->with('success', 'Paket ditolak. Harga sesi dikembalikan ke harga normal.');
    }
}