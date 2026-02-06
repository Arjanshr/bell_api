<?php
namespace App\Enums;

enum LeadSource: string {
    case WEBSITE = 'website';
    case PERSONAL_CONTACT = 'personal_contact';
    case REFERRAL = 'referral';
    case SOCIAL_MEDIA = 'social_media';
    case ADEVERTISING = 'advertising';
    case FCAN_DIRECTORY = 'fcan_directory';
    case SMS = 'sms';
    case WHATS_APP = 'whats_app';
    case VIBER = 'viber';
    case TELEMARKETING = 'telemarketing';
}