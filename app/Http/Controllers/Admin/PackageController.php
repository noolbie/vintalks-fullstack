<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PackageRequest;
use App\Models\Package;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PackageController extends Controller
{
    // ==== Manajemen paket layanan ====

    // Daftar paket dengan status aktif & penandaan "most popular".
    public function index(): View
    {
        return view('admin.packages.index', [
            'packages' => Package::withCount('bookings')->orderBy('price')->get(),
        ]);
    }

    // Form tambah paket baru.
    public function create(): View
    {
        return view('admin.packages.create');
    }

    // Simpan paket baru.
    public function store(PackageRequest $request): RedirectResponse
    {
        Package::create($this->validatedData($request));

        return redirect()->route('admin.packages.index')->with('success', 'Paket berhasil ditambahkan.');
    }

    // Form edit paket.
    public function edit(Package $package): View
    {
        return view('admin.packages.edit', compact('package'));
    }

    // Simpan perubahan paket.
    public function update(PackageRequest $request, Package $package): RedirectResponse
    {
        $package->update($this->validatedData($request));

        return redirect()->route('admin.packages.index')->with('success', 'Paket berhasil diperbarui.');
    }

    // Hapus paket (booking yang memakainya jadi tidak terhubung).
    public function destroy(Package $package): RedirectResponse
    {
        $package->delete();

        return back()->with('success', 'Paket berhasil dihapus.');
    }

    // ==== Helper ====

    // Siapkan data yang akan disimpan (bersihkan benefit kosong & flag boolean).
    private function validatedData(PackageRequest $request): array
    {
        $data = $request->validated();
        $data['benefits'] = array_values(array_filter(
            $request->input('benefits', []),
            fn ($b) => is_string($b) && trim($b) !== ''
        ));
        $data['is_popular'] = $request->boolean('is_popular');
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}