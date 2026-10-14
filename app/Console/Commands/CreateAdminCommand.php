<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdminCommand extends Command
{
    protected $signature = 'vantage:admin {email?}';
    protected $description = 'Create or promote a Vantage administrator account.';

    public function handle(): int
    {
        $email = $this->argument('email') ?: $this->ask('Admin email');
        $name = $this->ask('Admin name', 'Vantage Admin');
        $password = $this->secret('Password (minimum 12 characters)');

        if (strlen($password) < 12) {
            $this->error('Password must be at least 12 characters.');
            return self::FAILURE;
        }

        $user = User::updateOrCreate(['email' => $email], [
            'name' => $name,
            'password' => Hash::make($password),
            'is_admin' => true,
        ]);

        $this->info("Administrator {$user->email} is ready.");
        return self::SUCCESS;
    }
}
