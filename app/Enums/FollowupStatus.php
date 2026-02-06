<?php
namespace App\Enums;

enum FollowupStatus: string {
    case COMPLETED = '1';
    case INCOMPLETE = '0';
    case PROCESSING ='2';
}