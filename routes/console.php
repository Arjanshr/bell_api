<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Http;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();
Schedule::command('scout:import "App\\Models\\Product"')->everyFifteenMinutes();


Schedule::call(function () {
    Http::get(config('scout.meilisearch.host') . '/health');
})->everyFifteenMinutes();