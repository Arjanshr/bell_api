<?php
namespace App\Enums;

enum TransactionType: string {
    case PURCHASE = 'purchase';
    case SALE = 'sale';
    case PURCHASE_RETURN = 'purchase_return';
    case SALE_RETURN = 'sale_return';
}