<?php

namespace App\Enums;

enum ContactMessageStatus: string
{
    case UNREAD = 'unread';
    case READ = 'read';
    case ANSWERED = 'answered';

    public function label(): string
    {
        return match($this) {
            self::UNREAD => 'Unread',
            self::READ => 'Read',
            self::ANSWERED => 'Answered',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::UNREAD => 'danger',
            self::READ => 'warning',
            self::ANSWERED => 'success',
        };
    }
}
