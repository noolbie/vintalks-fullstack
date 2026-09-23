<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\MentorAvailability;
use App\Models\User;
use App\Services\DocumentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Tests\CreatesRoles;
use Tests\TestCase;

class DocumentAuthorizationTest extends TestCase
{
    use CreatesRoles;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRoles();
        Storage::fake('private');
    }

    private function makeDocumentFor(User $owner): \App\Models\Document
    {
        $file = UploadedFile::fake()->create('rab-pribadi.pdf', 100, 'application/pdf');

        return app(DocumentService::class)->store($owner, null, 'document', $file);
    }

    public function test_owner_can_download_their_document_via_signed_url(): void
    {
        $owner = $this->makeParticipant();
        $document = $this->makeDocumentFor($owner);

        $url = URL::temporarySignedRoute('documents.show', now()->addMinutes(5), ['document' => $document->id]);

        $response = $this->actingAs($owner)->get($url);

        $response->assertOk();
        $response->assertDownload($document->original_name);
    }

    public function test_other_user_cannot_download_someone_elses_document(): void
    {
        $owner = $this->makeParticipant();
        $document = $this->makeDocumentFor($owner);
        $intruder = $this->makeParticipant();

        $url = URL::temporarySignedRoute('documents.show', now()->addMinutes(5), ['document' => $document->id]);

        $response = $this->actingAs($intruder)->get($url);

        $response->assertForbidden();
    }

    public function test_mentor_can_download_documents_of_their_own_booking(): void
    {
        $participant = $this->makeParticipant();
        $mentor = $this->makeMentor();
        $date = now()->addDays(2)->format('Y-m-d');
        $slot = MentorAvailability::create([
            'mentor_id' => $mentor->id,
            'date' => $date,
            'start_time' => '10:00:00',
            'end_time' => '11:00:00',
            'status' => 'available',
        ]);

        $booking = Booking::create([
            'booking_code' => Booking::generateBookingCode(),
            'participant_id' => $participant->id,
            'mentor_id' => $mentor->user_id,
            'mentor_availability_id' => $slot->id,
            'session_date' => $date,
            'start_time' => $slot->start_time,
            'end_time' => $slot->end_time,
            'price' => $mentor->price,
            'booking_status' => 'payment_verification',
            'payment_status' => 'waiting_verification',
        ]);

        $file = UploadedFile::fake()->create('cv.pdf', 100, 'application/pdf');
        $document = app(DocumentService::class)->store($participant, $booking, 'document', $file);

        $url = URL::temporarySignedRoute('documents.show', now()->addMinutes(5), ['document' => $document->id]);

        $response = $this->actingAs($mentor->user)->get($url);

        $response->assertOk();
    }

    public function test_unsigned_url_is_rejected(): void
    {
        $owner = $this->makeParticipant();
        $document = $this->makeDocumentFor($owner);

        $response = $this->actingAs($owner)->get("/documents/{$document->id}");

        $response->assertForbidden();
    }
}