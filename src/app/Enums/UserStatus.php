<?php

namespace App\Enums;

enum UserStatus: string
{
    case Active = 'active';
    case Disabled = 'disabled';

    public static function getStatuses()
    {
        return [
            self::Active->value => 'アクティブ',
            self::Disabled->value => '非アクティブ',
        ];
    }
}
