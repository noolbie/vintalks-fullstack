<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    // ==== Manajemen user ====

    // Daftar semua user dengan filter role & pencarian nama/email.
    public function index(Request $request): View
    {
        $users = User::with('roles')
            ->when($request->filled('role'), fn ($q) => $q->role($request->input('role')))
            ->when($request->filled('q'), fn ($q) => $q->where(function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->string('q').'%')
                    ->orWhere('email', 'like', '%'.$request->string('q').'%');
            }))
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    // Hapus user (kecuali akun admin) beserta datanya.
    public function destroy(User $user): \Illuminate\Http\RedirectResponse
    {
        abort_if($user->isAdmin(), 403, 'Akun admin tidak dapat dihapus.');

        $user->delete();

        return back()->with('success', 'Pengguna berhasil dihapus.');
    }
}