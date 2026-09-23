<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TopicRequest;
use App\Models\Topic;
use Illuminate\View\View;

class TopicController extends Controller
{
    // ==== Manajemen topik ====

    // Daftar topik konsultasi beserta jumlah mentor yang mengambilnya.
    public function index(): View
    {
        return view('admin.topics.index', [
            'topics' => Topic::withCount('mentors')->orderBy('name')->paginate(15),
        ]);
    }

    // Tambah topik baru.
    public function store(TopicRequest $request): \Illuminate\Http\RedirectResponse
    {
        Topic::create($request->validated());

        return back()->with('success', 'Topik berhasil ditambahkan.');
    }

    // Perbarui nama/deskripsi topik.
    public function update(TopicRequest $request, Topic $topic): \Illuminate\Http\RedirectResponse
    {
        $topic->update($request->validated());

        return back()->with('success', 'Topik berhasil diperbarui.');
    }

    // Hapus topik konsultasi.
    public function destroy(Topic $topic): \Illuminate\Http\RedirectResponse
    {
        $topic->delete();

        return back()->with('success', 'Topik berhasil dihapus.');
    }
}