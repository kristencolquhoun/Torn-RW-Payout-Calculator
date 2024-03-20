<?php

namespace App\Console\Commands;

use App\Models\Chain;
use App\Models\Config;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

class ImportChainLogs extends Command
{
    protected $signature = 'app:import-chain-logs';

    protected int $interval = 720; // 12 hours

    public function handle()
    {
        $configTimestamp = Config::firstOrCreate([
            'key' => 'last_chain_update',
        ], [
            'value' => '1710608400',
        ]);

        $lastChainUpdate = Carbon::createFromTimestamp(
            $configTimestamp->value
        );

        $nextChainUpdate = $lastChainUpdate->copy()->addMinutes(
            $this->interval
        );

        if (now()->isAfter($nextChainUpdate)) {
            $response = Http::get('https://api.torn.com/faction', [
                'selections' => 'chains',
                'key' => env('TORN_API_KEY'),
                'from' => $lastChainUpdate->getTimestamp(),
                'to' => $nextChainUpdate->getTimestamp(),
            ]);

            if ($response->ok() && isset($response->json()['chains'])) {
                collect($response->json()['chains'])
                    ->each(function (array $chain, string $id) {
                        Chain::updateOrCreate([
                            'chain_id' => $id,
                        ], array_merge([
                            'chain_id' => $id,
                        ], $chain));
                    });
            } else {
                dd(
                    $response,
                    $response->json()
                );
            }

            $configTimestamp->update([
                'value' => $nextChainUpdate->getTimestamp(),
            ]);
        } else {
            dd('no more to upload');
        }
    }
}
