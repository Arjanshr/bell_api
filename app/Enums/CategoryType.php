<?php
namespace App\Enums;

enum CategoryType: string {
    case EXPENSES = 'expenses';
    case PROJECTS = 'projects';
    case LEADS = 'leads';
}