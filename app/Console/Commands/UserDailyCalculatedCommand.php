<?php

namespace App\Console\Commands;

use App\Models\Log\DeviceHourlyCalculated;
use App\Models\Log\UserDailyCalculated;
use App\Models\System\Device;
use Illuminate\Console\Command;

class UserDailyCalculatedCommand extends Command
{
    protected $signature = 'calculated:user-daily';
    protected $description = 'Kullanıcıların günlük verilerini hesaplar.';

    public function handle(): void
    {
        foreach (Device::get() as $device) {
            $models = DeviceHourlyCalculated::whereNull('calculated_at')
                ->where('user_id', $device->user_id)
                ->select('mac', DB::raw('SUM(value) as value'))
                ->groupBy('mac')
                ->get();

            foreach ($models as $model) {
                UserDailyCalculated::create([
                    'value' => $model->value,
                    'calculated_at' => now(),
                ]);
            }

            DeviceHourlyCalculated::whereNull('calculated_at')
                ->where('user_id', $device->user_id)
                ->update(['calculated_at' => now()]);
        }
    }
}
