<?php

use App\Console\Commands\ImportAttackLogs;
use App\Console\Commands\ImportChainLogs;
use App\Exports\RankedWarPointsExport;
use App\Models\Attack;
use App\Models\Chain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Facades\Excel;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/attacks', function () {
    dump(Attack::count());
    Artisan::call(ImportAttackLogs::class);
    dump(Attack::count());
});

Route::get('/chains', function () {
    dump(Chain::count());
    Artisan::call(ImportChainLogs::class);
    dump(Chain::count());
});

Route::post('/', function (Request $request) {
    return Excel::download(new RankedWarPointsExport, 'ranked.csv');
});
