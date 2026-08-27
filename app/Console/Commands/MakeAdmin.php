<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class MakeAdmin extends Command
{
    protected $signature = 'puente:make-admin {email} {--revoke : Take the admin rights away instead}';

    protected $description = 'Grant or revoke access to the service admin for one account';

    public function handle(): int
    {
        $user = User::firstWhere('email', $this->argument('email'));

        if ($user === null) {
            $this->components->error("No account found for {$this->argument('email')}.");

            return self::FAILURE;
        }

        $grant = ! $this->option('revoke');

        // is_admin is deliberately outside the model's fillable list, so it is
        // set directly rather than through mass assignment.
        $user->is_admin = $grant;
        $user->save();

        $this->components->info(
            $grant
                ? "{$user->email} can now manage services."
                : "{$user->email} no longer has admin access.",
        );

        return self::SUCCESS;
    }
}
