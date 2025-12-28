<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Auto-delete berita di trash yang sudah lebih dari 30 hari
Schedule::command('model:prune')->daily();

// Flush cached news views to database every 10 minutes
// This reduces database writes by batching view count updates
Schedule::command('news:flush-views')->everyTenMinutes();
