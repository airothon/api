<?php

namespace App\Console\Commands;

use App\Models\Log\DeviceHourlyCalculated;
use App\Models\Log\DeviceLog;
use Illuminate\Console\Command;

class DeviceHourlyCalculatedCommand extends Command
{
    protected $signature = 'calculated:device-hourly';
    protected $description = 'Cihazlara ait saatlik verileri hesaplar.';

    public function handle(): void
    {
        $deviceLogs = DeviceLog::whereNull('calculated_at')->select('mac', DB::raw('SUM(value1) as value'))
            ->groupBy('mac')
            ->get();

        foreach ($deviceLogs as $deviceLog) {
            DeviceHourlyCalculated::create([
                'mac' => $deviceLog->mac,
                'value' => $deviceLog->value,
                'calculated_at' => now(),
            ]);
        }

        DeviceLog::whereNull('calculated_at')->update(['calculated_at' => now()]);
    }
}
