<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use App\Models\User;

#[Signature('app:clear-inactive-users')]
#[Description('Command description')]
class ClearInactiveUsers extends Command
{
    // old way
    // protected $signature = 'users:clear-inactive';
    // protected $description = 'Delete inactive users';

    public function handle()
    {
        User::where('is_active', 0)->delete();
        $this->info('Inactive users deleted successfully.');
        return Command::SUCCESS;
    }
}
