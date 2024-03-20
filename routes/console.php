<?php

use App\Console\Commands\ImportAttackLogs;
use App\Console\Commands\ImportChainLogs;
use Illuminate\Support\Facades\Schedule;

Schedule::command(ImportChainLogs::class)->hourly();
Schedule::command(ImportAttackLogs::class)->everyMinute();
