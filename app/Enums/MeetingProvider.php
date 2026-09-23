<?php

namespace App\Enums;

enum MeetingProvider: string
{
    case GoogleMeet = 'google_meet';
    case Zoom = 'zoom';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::GoogleMeet => 'Google Meet',
            self::Zoom => 'Zoom',
            self::Other => 'Lainnya',
        };
    }
}