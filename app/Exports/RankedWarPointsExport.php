<?php

namespace App\Exports;

use App\Models\Attack;
use App\Models\Chain;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RankedWarPointsExport implements FromArray, WithHeadings
{
    protected $attacks;

    protected $chains;

    protected $members;

    protected $rankedWarFaction;

    public function __construct()
    {
        [$from, $to] = explode(' until ', request()->input('timeframe', '17:00:00 - 16/03/24 until 19:05:12 - 18/03/24'));

        $this->attacks = Attack::whereBetween('timestamp_ended', [
            Carbon::createFromFormat('H:i:s - d/m/y', $from),
            Carbon::createFromFormat('H:i:s - d/m/y', $to),
        ])->get();

        $this->rankedWarFaction = $this->attacks
            ->where('attacker_faction', env('TORN_FACTION_ID'))
            ->firstWhere('ranked_war', true)->defender_faction;

        $this->chains = Chain::all();

        $this->members = $this->attacks
            ->filter(function (Attack $attack) {
                return in_array(env('TORN_FACTION_ID'), [
                    $attack->attacker_faction,
                    $attack->defender_faction
                ]);
            })
            ->unique(function (Attack $attack) {
                if ($attack->attacker_faction == env('TORN_FACTION_ID')) {
                    return $attack->attacker_id;
                }

                return $attack->defender_id;
            })
            ->mapWithKeys(function (Attack $attack) {
                $id = $attack->attacker_faction == env('TORN_FACTION_ID') ? $attack->attacker_id : $attack->defender_id;
                $name = $attack->attacker_faction == env('TORN_FACTION_ID') ? $attack->attacker_name : $attack->defender_name;

                return [$id => [
                    'user' => $name . ' [' . $id . ']',
                    'war_attacks' => 0,
                    'war_losses' => 0,
                    'war_assists' => 0,
                    'war_retaliations' => 0,
                    'war_mugs' => 0,
                    'chain_attacks' => 0,
                    'chain_assists' => 0,
                    'chain_mugs' => 0,
                    'friendly_hits' => 0,
                    'friendly_loss' => 0,
                ]];
            })->toArray();
    }

    public function array(): array
    {
        $this->attacks->each(function (Attack $attack) {
            if ($attack->ranked_war || $attack->defender_faction === $this->rankedWarFaction) {
                if ($attack->attacker_faction == env('TORN_FACTION_ID')) {
                    switch ($attack->result) {
                        case 'Attacked':
                            $this->members[$attack->attacker_id]['war_attacks']++;
                            break;
                        case 'Hospitalized':
                            if ($attack->modifiers['retaliation'] !== 1) {
                                $this->members[$attack->attacker_id]['war_retaliations']++;
                            } else {
                                $this->members[$attack->attacker_id]['war_attacks']++;
                            }
                            break;
                        case 'Assist':
                            $this->members[$attack->attacker_id]['war_assists']++;
                            break;
                        case 'Mugged':
                            $this->members[$attack->attacker_id]['war_mugs']++;
                            break;
                        case 'Lost':
                        case 'Stalemate':
                        case 'Timeout':
                        case 'Interrupted':
                        case 'Escape':
                            break;
                        default:
                            dd('MISSING ATTACK #1', $attack);
                            break;
                    }
                } else {
                    switch ($attack->result) {
                        case 'Attacked':
                        case 'Hospitalized':
                        case 'Mugged':
                            $this->members[$attack->defender_id]['war_losses']++;
                            break;
                        default:
                            dd('MISSING ATTACK #2', $attack);
                            break;
                    }
                }
            } else {
                // Find the chain
                if ($attack->attacker_faction == env('TORN_FACTION_ID')) {

                    if ($attack->defender_faction == env('TORN_FACTION_ID')) {
                        $this->members[$attack->attacker_id]['friendly_hits']++;
                        $this->members[$attack->defender_id]['friendly_loss']++;
                    }

                    $chain = $this->chains->filter(function (Chain $chain) use ($attack) {
                        return $chain->start->isBefore($attack->timestamp_ended) && $chain->end->isAfter($attack->timestamp_ended);
                    })->first();

                    if ($chain) {
                        switch ($attack->result) {
                            case 'Attacked':
                            case 'Hospitalized':
                            case 'Special':
                                $this->members[$attack->attacker_id]['chain_attacks']++;
                                break;
                            case 'Assist':
                                $this->members[$attack->attacker_id]['chain_assists']++;
                                break;
                            case 'Mugged':
                                $this->members[$attack->attacker_id]['chain_mugs']++;
                                break;
                            case 'Lost':
                            case 'Stalemate':
                            case 'Escape':
                                break;
                            default:
                                dd('MISSING ATTACK #3', $attack);
                                break;
                        }
                    }
                }
            }
        });

        return $this->members;
    }

    public function headings(): array
    {
        return [
            'User',
            'War Attacks',
            'War Losses',
            'War Assists',
            'War Retals',
            'War Mugs',
            'Chain Attacks',
            'Chain Assists',
            'Chain Mugs',
            'Friendly Hits',
            'Friendly Loss',
        ];
    }
}
