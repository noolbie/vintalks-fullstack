<?php

namespace Database\Seeders;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Models\Booking;
use App\Models\ConsultationRequirement;
use App\Models\Document;
use App\Models\MentorAvailability;
use App\Models\MentorProfile;
use App\Models\ParticipantProfile;
use App\Models\Payment;
use App\Models\Topic;
use App\Models\User;
use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        \Spatie\Permission\Models\Role::query()->upsert([
            ['guard_name' => 'web', 'name' => 'admin'],
            ['guard_name' => 'web', 'name' => 'mentor'],
            ['guard_name' => 'web', 'name' => 'participant'],
        ], ['name', 'guard_name']);

        $admin = User::firstOrCreate(
            ['email' => 'admin@vintalks.net'],
            ['name' => 'Admin VinTalks', 'password' => Hash::make('password')]
        );
        $admin->syncRoles('admin');

        $mentorUsers = User::firstOrCreate(
            ['email' => 'diyanarif@vintalks.net'],
            ['name' => 'Dian Arif Saputra', 'password' => Hash::make('password')]
        );
        $mentorUsers->syncRoles('mentor');
        if (! $mentorUsers->mentorProfile()->exists()) {
            $mentorUsers->mentorProfile()->create([
                'display_name' => 'Dian Arif Saputra',
                'expertise' => 'Cyber Security & Penetration Testing, 8+ tahun di bidang keamanan siber',
                'bio' => 'Berpengalaman melakukan penetration testing, vulnerability assessment, dan security assessment pada aplikasi web, jaringan, serta infrastruktur untuk membantu organisasi mengidentifikasi dan memperbaiki risiko keamanan.',
                'experience' => 'Senior Penetration Tester & Cyber Security Consultant.',
                'price' => 500000,
                'is_active' => true,
            ]);
        }
        // Ketersediaan untuk mentor demo
        $demoMentor = $mentorUsers->mentorProfile()->first();
        if ($demoMentor && ! $demoMentor->availabilities()->exists()) {
            foreach (range(1, 10) as $offset) {
                $date = now()->addDays($offset);
                if ($date->isSunday()) {
                    continue;
                }
                foreach ([9, 14, 16] as $startHour) {
                    MentorAvailability::create([
                        'mentor_id' => $demoMentor->id,
                        'date' => $date->format('Y-m-d'),
                        'start_time' => sprintf('%02d:00:00', $startHour),
                        'end_time' => sprintf('%02d:00:00', $startHour + 1),
                        'status' => 'available',
                    ]);
                }
            }
        }

        $participantUser = User::firstOrCreate(
            ['email' => 'participant@vintalks.net'],
            ['name' => 'Kurt Cobain', 'password' => Hash::make('password')]
        );
        $participantUser->syncRoles('participant');
        if (! $participantUser->participantProfile) {
            $participantUser->participantProfile()->create([
                'phone' => '081234567890',
                'occupation' => 'Fresh Graduate',
                'institution' => 'Universitas Muhammadiyah 2 Sidoarjo',
            ]);
        }


        $topics = collect([
            ['name' => 'Web Application Pentest', 'description' => 'Identifikasi dan analisis kerentanan keamanan pada aplikasi web.'],
            ['name' => 'API Security Testing', 'description' => 'Pengujian keamanan API terhadap autentikasi, otorisasi, dan kerentanan lainnya.'],
            ['name' => 'Network Pentest', 'description' => 'Pengujian keamanan jaringan dan identifikasi potensi celah pada infrastruktur.'],
            ['name' => 'Vulnerability Assessment', 'description' => 'Identifikasi, validasi, dan analisis tingkat risiko kerentanan sistem.'],
            ['name' => 'Security Assessment', 'description' => 'Evaluasi konfigurasi dan keamanan aplikasi, server, serta infrastruktur.'],
            ['name' => 'Laporan & Remediation', 'description' => 'Penyusunan laporan temuan beserta rekomendasi perbaikan keamanan.'],
        ])->map(fn ($data) => Topic::firstOrCreate(['name' => $data['name']], $data));

        // Pengaturan pembayaran default
        Setting::set('payment_bank_name', 'BCA');
        Setting::set('payment_account_name', 'VinTalks');
        Setting::set('payment_account_number', '081329393939');
        Setting::set('payment_methods', ['qris', 'transfer_bank', 'digital_wallet']);
        Setting::set('commission_rate', 15);

        // Demo bookings for the seeded demo participant
        $demoMentor = $mentorUsers->mentorProfile()->first();
        if ($demoMentor && $participantUser) {
            $booking = Booking::firstOrCreate(
                ['booking_code' => 'VT-DEMO-000001'],
                [
                    'participant_id' => $participantUser->id,
                    'mentor_id' => $demoMentor->user_id,
                    'session_date' => now()->addDays(2)->format('Y-m-d'),
                    'start_time' => '10:00:00',
                    'end_time' => '11:00:00',
                    'price' => $demoMentor->price,
                    'booking_status' => BookingStatus::Confirmed->value,
                    'payment_status' => PaymentStatus::Verified->value,
                    'meeting_provider' => 'google_meet',
                    'meeting_url' => 'https://meet.google.com/demo-vintalks',
                    'confirmed_at' => now(),
                ]
            );

            $booking->requirement()->firstOrCreate([
                'linkedin_url' => 'https://www.linkedin.com/in/kurt-cobain',
                'career_goal' => 'Mendapatkan pekerjaan remote sebagai junior pentester.',
                'consultation_topic' => 'Review portofolio & simulasi interview',
                'description' => 'Ingin menyiapkan portofolio dan berlatih cyber security.',
            ]);

            if ($booking->payment_status === PaymentStatus::Verified->value && ! $booking->payment) {
                $booking->payment()->create([
                    'amount' => $booking->price,
                    'payment_method' => 'transfer_bank',
                    'payment_reference' => 'DEMO-REF-01',
                    'submitted_at' => now()->subDay(),
                    'verified_at' => now(),
                    'status' => PaymentStatus::Verified->value,
                ]);

                $proof = Document::create([
                    'user_id' => $participantUser->id,
                    'booking_id' => $booking->id,
                    'document_type' => 'payment_proof',
                    'original_name' => 'bukti-transfer-demo.jpg',
                    'file_name' => 'demo-proof.jpg',
                    'file_path' => 'demo/bukti-transfer-demo.jpg',
                    'mime_type' => 'image/jpeg',
                    'file_size' => 1000,
                ]);
                $booking->payment()->update(['proof_document_id' => $proof->id]);
            }
        }

        // Ensure a waiting-verification booking exists for admin demo
        if ($demoMentor && $participantUser) {
            $availabilityId = $demoMentor->availabilities()->where('status', 'available')->where('date', '>=', now()->addDays(3)->format('Y-m-d'))->value('id');
            $slot = $availabilityId
                ? $demoMentor->availabilities()->find($availabilityId)
                : $demoMentor->availabilities()->first();

            if ($slot) {
                $waiting = Booking::firstOrCreate(
                    ['booking_code' => 'VT-DEMO-000002'],
                    [
                        'participant_id' => $participantUser->id,
                        'mentor_id' => $demoMentor->user_id,
                        'mentor_availability_id' => $slot->id,
                        'session_date' => $slot->date,
                        'start_time' => $slot->start_time,
                        'end_time' => $slot->end_time,
                        'price' => $demoMentor->price,
                        'booking_status' => BookingStatus::PaymentVerification->value,
                        'payment_status' => PaymentStatus::WaitingVerification->value,
                    ]
                );

                if (! $waiting->payment) {
                    $proof = Document::create([
                        'user_id' => $participantUser->id,
                        'booking_id' => $waiting->id,
                        'document_type' => 'payment_proof',
                        'original_name' => 'bukti-transfer-demo-2.jpg',
                        'file_name' => 'demo-proof-2.jpg',
                        'file_path' => 'demo/bukti-transfer-demo-2.jpg',
                        'mime_type' => 'image/jpeg',
                        'file_size' => 1000,
                    ]);
                    $waiting->payment()->create([
                        'amount' => $waiting->price,
                        'payment_method' => 'qris',
                        'proof_document_id' => $proof->id,
                        'submitted_at' => now(),
                        'status' => PaymentStatus::WaitingVerification->value,
                    ]);
                }
            }
        }

        // Demo sesi selesai (untuk fitur pendapatan & transaksi mentor). Admin tinggal "Tandai Dibayar".
        if ($demoMentor && $participantUser) {
            $completedSlot = $demoMentor->availabilities()
                ->where('status', 'available')
                ->where('date', '>=', now()->addDays(5)->format('Y-m-d'))
                ->orderBy('date')
                ->first();

            if ($completedSlot) {
                $completed = Booking::firstOrCreate(
                    ['booking_code' => 'VT-DEMO-000003'],
                    [
                        'participant_id' => $participantUser->id,
                        'mentor_id' => $demoMentor->user_id,
                        'mentor_availability_id' => $completedSlot->id,
                        'session_date' => $completedSlot->date,
                        'start_time' => $completedSlot->start_time,
                        'end_time' => $completedSlot->end_time,
                        'price' => $demoMentor->price,
                        'booking_status' => BookingStatus::Completed->value,
                        'payment_status' => PaymentStatus::Verified->value,
                        'meeting_provider' => 'google_meet',
                        'meeting_url' => 'https://meet.google.com/demo-selesai',
                        'confirmed_at' => now()->subDays(7),
                        'completed_at' => now()->subDay(),
                    ]
                );

                if (! $completed->payment) {
                    $completed->payment()->create([
                        'amount' => $completed->price,
                        'payment_method' => 'transfer_bank',
                        'payment_reference' => 'DEMO-REF-03',
                        'submitted_at' => now()->subDays(8),
                        'verified_at' => now()->subDays(7),
                        'status' => PaymentStatus::Verified->value,
                    ]);
                }
            }
        }
    }
}
