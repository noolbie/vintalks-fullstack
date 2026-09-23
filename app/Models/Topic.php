<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Model Topic: kategori/topik konsultasi yang bisa dipilih mentor (mis. "Karir di Big Tech").
 */
class Topic extends Model
{
    /** @use HasFactory<\Database\Factories\TopicFactory> */
    use HasFactory;

    // Kolom yang boleh diisi user (mass assignment).
    protected $fillable = [
        'name',
        'description',
    ];

    // ==== Relasi antar tabel ====

    // Topik dipilih oleh banyak mentor (melalui tabel pivot `mentor_topic`).
    public function mentors(): BelongsToMany
    {
        return $this->belongsToMany(MentorProfile::class, 'mentor_topic', 'topic_id', 'mentor_id');
    }
}