<?php

namespace App\Console\Commands;

use App\Models\Attack;
use App\Models\Config;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

class ImportAttackLogs extends Command
{
    protected $signature = 'app:import-attack-logs';

    protected int $interval = 30 * 1;

    public function handle()
    {
        $configTimestamp = Config::firstOrCreate([
            'key' => 'last_attack_update',
        ], [
            'value' => '17154073060',
        ]);

        dump($configTimestamp);

        $lastAttackUpdate = Carbon::createFromTimestamp(
            $configTimestamp->value
        );

        $nextAttackUpdate = $lastAttackUpdate->copy()->addMinutes(
            $this->interval
        );

        if (now()->isAfter($nextAttackUpdate)) {
            $response = Http::get('https://api.torn.com/faction', [
                'selections' => 'attacks',
                'key' => env('TORN_API_KEY'),
                'from' => $lastAttackUpdate->getTimestamp(),
                'to' => $nextAttackUpdate->getTimestamp(),
            ]);

            if ($response->ok() && isset($response->json()['attacks'])) {
                collect($response->json()['attacks'])
                    ->each(function (array $attack, string $id) {
                        Attack::updateOrCreate(
                            ['attack_id' => $id],
                            array_merge(
                                ['attack_id' => $id],
                                Arr::where(Arr::except($attack, ['code']), fn ($value) => is_array($value) || strlen($value))
                            )
                        );
                    });
            } else {
                dd(
                    $response,
                    $response->json(),
                    $configTimestamp->value
                );
            }

            $configTimestamp->update([
                'value' => $nextAttackUpdate->getTimestamp(),
            ]);
        } else {
            dd('no more to upload');
        }
    }
}
