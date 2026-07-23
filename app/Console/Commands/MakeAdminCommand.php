<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class MakeAdminCommand extends Command
{
    protected $signature = 'game:make-admin {--email=admin@123.com} {--password=ywxq}';

    protected $description = 'Create or update the default admin user.';

    public function handle(): int
    {
        $email = (string) $this->option('email');

        User::query()->updateOrCreate(
            ['email' => $email],
            [
                'name' => 'admin',
                'password' => Hash::make((string) $this->option('password')),
            ],
        );

        $this->info("管理员账号已准备好：{$email}");

        return self::SUCCESS;
    }
}
