@extends('layouts.app')

@section('title', 'Isi Hasil Konsultasi')

@section('content')
    <a href="{{ route('mentor.bookings.show', $booking) }}" class="text-sm text-slate-600 hover:text-[#001D3D]">&larr; Kembali ke detail booking</a>

    <div class="bg-white rounded-xl border border-slate-200 mt-4 p-6 max-w-3xl">
        <h1 class="text-xl font-bold text-slate-800">Hasil Konsultasi — {{ $booking->booking_code }}</h1>
        <p class="text-sm text-slate-500 mt-1">Peserta: {{ $booking->participant->name }} • {{ $booking->session_date->format('d M Y') }}</p>

        <form method="POST" action="{{ route('mentor.consultation-results.store', $booking) }}" enctype="multipart/form-data" class="mt-6 space-y-5">
            @csrf

            <div>
                <label for="summary" class="block text-sm font-semibold text-slate-700 mb-2">Ringkasan Hasil</label>
                <textarea name="summary" id="summary" rows="6" required class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#FFC300]"
                          placeholder="Rangkuman hasil sesi konsultasi untuk peserta…">{{ old('summary') }}</textarea>
            </div>

            <div>
                <label for="mentor_notes" class="block text-sm font-semibold text-slate-700 mb-2">Catatan Mentoring</label>
                <textarea name="mentor_notes" id="mentor_notes" rows="4" class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#FFC300]"
                          placeholder="Catatan pribadi / rekomendasi yang hanya menambah konteks (opsional)…">{{ old('mentor_notes') }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Dokumen Hasil (opsional)</label>
                <div id="doc-rows" class="space-y-3">
                    <div class="doc-row grid grid-cols-1 sm:grid-cols-3 gap-3 items-center bg-slate-50 rounded-xl p-3">
                        <input type="text" name="documents[0][title]" placeholder="Judul dokumen"
                               class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
                        <select name="documents[0][document_type]" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
                            <option value="review_cv">Review CV</option>
                            <option value="review_linkedin">Review LinkedIn</option>
                            <option value="assessment">Assessment</option>
                            <option value="consultation_result">Hasil Konsultasi</option>
                            <option value="notes">Catatan</option>
                            <option value="other">Lainnya</option>
                        </select>
                        <input type="file" name="documents[0][file]" class="text-sm">
                    </div>
                </div>
                <button type="button" id="add-doc" class="mt-3 text-sm font-semibold text-[#001D3D] hover:underline"><i class="ri-add-line"></i> Tambah dokumen</button>
                <p class="text-xs text-slate-500 mt-2">Format PDF, DOC, JPG, PNG. Maksimal 10MB per file.</p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('mentor.bookings.show', $booking) }}" class="px-5 py-2.5 rounded-lg border border-slate-300 text-sm font-medium">Kembali</a>
                <button type="submit" class="bg-[#FFC300] hover:bg-amber-400 text-[#001D3D] font-semibold px-8 py-2.5 rounded-lg text-sm">Simpan Hasil</button>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        let docIndex = 1;
        document.getElementById('add-doc').addEventListener('click', () => {
            const row = document.querySelector('.doc-row').cloneNode(true);
            row.querySelectorAll('input').forEach(el => { el.value = ''; });
            row.querySelector('select').selectedIndex = 0;
            row.querySelector('input[name^="documents"]').name = 'documents[' + docIndex + '][title]';
            row.querySelector('select').name = 'documents[' + docIndex + '][document_type]';
            row.querySelector('input[type="file"]').name = 'documents[' + docIndex + '][file]';
            document.getElementById('doc-rows').appendChild(row);
            docIndex++;
        });
    </script>
    @endpush
@endsection