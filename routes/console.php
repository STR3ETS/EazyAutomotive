<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Ververst dagelijks de marktdata voor de taxatie-engine (eigen voorraad + live
// feed) en ruimt advertenties op die niet vers meer zijn, zodat de waardes actueel blijven.
Schedule::command('market:ingest --prune=90')->dailyAt('04:00')->withoutOverlapping();
