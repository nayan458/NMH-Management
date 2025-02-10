<?php

use App\Jobs\AddMessFineToStudents;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

app()->make(\Illuminate\Console\Scheduling\Schedule::class)->job(new AddMessFineToStudents)->everyTwoSeconds();