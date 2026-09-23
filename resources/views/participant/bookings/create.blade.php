{{-- Halaman form booking sesi: pilih topik, tanggal/jam slot, dan isi syarat konsultasi untuk mentor --}}
@extends('layouts.app')

@section('title', 'Booking Sesi')

@section('content')
    <a href="{{ route('participant.mentors.show', $mentor) }}" class="text-sm text-slate-600 hover:text-[#001D3D]">&larr; Kembali ke profil mentor</a>

    <div class="bg-white rounded-xl border border-slate-200 mt-4 p-6">
        <div class="flex items-center gap-4 pb-4 border-b border-slate-100">
            @if ($mentor->photo_url)
                <img src="{{ $mentor->photo_url }}" class="h-14 w-14 rounded-xl object-cover" alt="{{ $mentor->display_name }}">
            @else
                <i class="ri-user-star-line text-4xl text-slate-300"></i>
            @endif
            <div>
                <h1 class="text-lg font-bold text-slate-800">Booking — {{ $mentor->display_name }}</h1>
                <p class="text-sm text-slate-500">{{ $mentor->expertise }}</p>
            </div>
        </div>

        <form method="POST" action="{{ route('participant.mentors.bookings.store', $mentor) }}" enctype="multipart/form-data" class="mt-6 space-y-6">
            @csrf

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">1. Pilih Tanggal & Jam</label>
                @if (count($availableDates))
                    <div class="flex flex-wrap gap-2 mb-3">
                        @foreach ($availableDates as $date)
                            <button type="button" data-date="{{ \Illuminate\Support\Carbon::parse($date)->format('Y-m-d') }}"
                                    class="date-pill px-4 py-1.5 rounded-lg text-sm border border-slate-300 hover:border-[#001D3D]">
                                {{ \Illuminate\Support\Carbon::parse($date)->format('D, d M') }}
                            </button>
                        @endforeach
                    </div>
                    <div id="slot-list" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2">
                        <p class="col-span-full text-sm text-slate-500">Pilih tanggal terlebih dahulu untuk melihat jam tersedia.</p>
                    </div>
                @else
                    <p class="text-sm text-rose-600">Mentor ini belum memiliki jadwal tersedia.</p>
                @endif
                <input type="hidden" name="slot_id" id="slot_id">
                <input type="hidden" name="session_date" id="session_date">
                <input type="hidden" name="start_time" id="start_time">
                <input type="hidden" name="end_time" id="end_time">
            </div>

            <div>
                <label for="topic_id" class="block text-sm font-semibold text-slate-700 mb-2">2. Topik Konsultasi (opsional)</label>
                @if ($topics->isEmpty())
                    <p class="text-sm text-slate-500 border border-dashed border-slate-300 rounded-lg px-4 py-3">Mentor ini belum memiliki topik yang terdaftar.</p>
                @else
                    <select name="topic_id" id="topic_id" class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#FFC300]">
                        <option value="">— Pilih Topik —</option>
                        @foreach ($topics as $topic)
                            <option value="{{ $topic->id }}">{{ $topic->name }}</option>
                        @endforeach
                    </select>
                @endif
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">3. Informasi Konsultasi</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <input type="url" name="linkedin_url" value="{{ old('linkedin_url') }}" placeholder="URL Profil LinkedIn (opsional)"
                           class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#FFC300]">
                    <input type="text" name="consultation_topic" value="{{ old('consultation_topic') }}" placeholder="Judul singkat topik yang ingin dibahas"
                           class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#FFC300]">
                </div>
                <div class="grid grid-cols-1 gap-4 mt-4">
                    <textarea name="career_goal" rows="3" placeholder="Apa tujuan karier Anda? (opsional)"
                              class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#FFC300]">{{ old('career_goal') }}</textarea>
                    <textarea name="description" rows="3" placeholder="Deskripsi kondisi/situasi Anda saat ini…"
                              class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#FFC300]">{{ old('description') }}</textarea>
                    <textarea name="additional_notes" rows="2" placeholder="Catatan tambahan untuk mentor (opsional)"
                              class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#FFC300]">{{ old('additional_notes') }}</textarea>
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">4. Lampiran Dokumen (opsional)</label>
                <p class="text-xs text-slate-500 mb-3">Unggah CV atau dokumen pendukung agar mentor dapat mempersiapkan diri. Maksimal 5MB per file.</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-medium text-slate-600 block mb-1">CV</label>
                        <input type="file" name="documents[cv]" class="w-full text-sm">
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-600 block mb-1">Dokumen Pendukung</label>
                        <input type="file" name="documents[supporting_document]" class="w-full text-sm">
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between bg-slate-50 border border-slate-200 rounded-xl px-5 py-4">
                <div>
                    <p class="text-sm text-slate-600">Total pembayaran:</p>
                    <p class="text-2xl font-bold text-[#001D3D]">{{ $mentor->price_formatted }}</p>
                    <p class="text-xs text-slate-500">Pembayaran dilakukan setelah booking dibuat.</p>
                </div>
                <button type="submit" class="bg-[#FFC300] hover:bg-amber-400 text-[#001D3D] font-semibold px-8 py-3 rounded-lg">
                    Buat Booking
                </button>
            </div>
        </form>
    </div>

    <style>
        .date-pill { cursor: pointer; }
        .date-pill.active { background: #001D3D; color: #fff; border-color: #001D3D; }
        .slot-btn { cursor: pointer; }
        .slot-btn.sortable-chosen, .slot-btn:disabled { cursor: not-allowed; opacity: .5; }
        .slot-btn.selected { background: #001D3D; color: #fff; border-color: #001D3D; }
    </style>

    @push('scripts')
    <script>
        const mentorSlotsUrl = "{{ route('participant.mentors.slots', $mentor) }}";
        let selectedSlot = null;

        document.querySelectorAll('.date-pill').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.date-pill').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                loadSlots(btn.dataset.date);
            });
        });

        async function loadSlots(date) {
            const list = document.getElementById('slot-list');
            list.innerHTML = '<p class="col-span-full text-sm text-slate-500">Memuat jadwal…</p>';

            try {
                const res = await fetch(mentorSlotsUrl + '?date=' + date);
                const json = await res.json();

                if (!json.success) {
                    list.innerHTML = '<p class="col-span-full text-sm text-rose-600">' + json.message + '</p>';
                    return;
                }

                if (!json.data.length) {
                    list.innerHTML = '<p class="col-span-full text-sm text-slate-500">Tidak ada slot tersedia pada tanggal ini.</p>';
                    return;
                }

                list.innerHTML = '';
                json.data.forEach(slot => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'slot-btn px-4 py-2 rounded-lg text-sm border border-slate-300';
                    btn.textContent = slot.start_time + ' – ' + slot.end_time;
                    btn.dataset.id = slot.id;

                    if (!slot.available) {
                        btn.disabled = true;
                        btn.textContent += ' (terbooking)';
                        btn.classList.add('opacity-40');
                    } else {
                        btn.addEventListener('click', () => {
                            document.querySelectorAll('.slot-btn').forEach(b => b.classList.remove('selected'));
                            btn.classList.add('selected');
                            selectedSlot = slot;
                            document.getElementById('slot_id').value = slot.id;
                            document.getElementById('session_date').value = slot.date;
                            document.getElementById('start_time').value = slot.start_time;
                            document.getElementById('end_time').value = slot.end_time;
                        });
                    }
                    list.appendChild(btn);
                });
            } catch (e) {
                list.innerHTML = '<p class="col-span-full text-sm text-rose-600">Terjadi kesalahan saat memuat jadwal.</p>';
            }
        }
    </script>
    @endpush
@endsection