<?php

namespace App\Enums;

enum SettingType: string
{
    case TEXT = 'text';
    case TEXTAREA = 'textarea';
    case BOOLEAN = 'boolean';
    case SELECT = 'select';
    case IMAGE = 'image';
}
