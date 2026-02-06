<?php
namespace App\Enums;

enum PaymentType: string {
    case CASH = 'cash';
    case CARD = 'card';
    case WALLET = 'wallet';
    case MIXED = 'mixed';
    case Others = 'others';
}