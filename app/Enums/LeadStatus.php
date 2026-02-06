<?php
namespace App\Enums;

enum LeadStatus: string {
    case NEW = 'new';
    case CONTACTED = 'contacted';
    case CONVERTED = 'converted';
    case CLOSED = 'closed';
    case COLLECTION = 'collection';
}