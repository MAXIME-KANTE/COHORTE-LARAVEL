<?php

namespace App\Enums;

enum VerdictModeration: string
{
    case Acceptable = 'acceptable';
    case Douteux = 'douteux';
    case Inacceptable = 'inacceptable';
    case Indisponible = 'indisponible';

    public function statutPublication(): string
    {
        return match ($this) {
            self::Acceptable => 'publie',
            self::Douteux => 'en_moderation',
            self::Inacceptable => 'refuse',
            self::Indisponible => config('cohorte.moderation_fail_open') ? 'publie' : 'en_moderation',
        };
    }
}
