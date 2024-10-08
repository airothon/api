<?php

namespace App\Console\Commands;

use App\Models\Log\UserDailyCalculated;
use Illuminate\Console\Command;

class TokenMintCommand extends Command
{
    protected $signature = 'calculated:token-mint';
    protected $description = 'Mint tokens for users';

    public function handle(): void
    {
        foreach (UserDailyCalculated::whereNull('token_minted_at')->get() as $model) {
            exec('spl-token mint ' . env('token_id') . ' ' .  $model->value .  ' ' . $model->user->account_address);
        }
    }
}
