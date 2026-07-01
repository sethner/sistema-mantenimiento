<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Programar tareas automáticas de mantenimiento
Schedule::command('mantenimiento:check-preventivo')->daily();
Schedule::command('mantenimiento:check-vencidos')->daily();

