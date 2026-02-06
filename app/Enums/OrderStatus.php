<?php
namespace App\Enums;

enum OrderStatus: string {
    case PENDING ='pending';
    case PROCESSING ='processing';
    case CONFIRMED ='confirmed';
    case ON_HOLD ='on_hold';
    case SENT_FOR_DELIVERY ='sent_for_delivery';
    case COMPLETED ='completed';
    case CANCELLED ='cancelled';
    case RETURNED ='returned';
}