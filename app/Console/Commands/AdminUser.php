<?php

namespace App\Console\Commands;

use Filament\Commands\MakeUserCommand;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Modules\User\App\Models\User;
use Spatie\Permission\Models\Role;

use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

class AdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:admin-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create an admin user';

    protected array $options;

    protected function getUserData(): array
    {
        return [
            'name' => text(
                label: 'Name',
                required: true,
            ),

            'email' => text(
                label: 'Email address',
                required: true,
                validate: fn (string $email): ?string => match (true) {
                    !filter_var($email, FILTER_VALIDATE_EMAIL) => 'The email address must be valid.',
                    User::where('email', $email)->exists() => 'A user with this email address already exists',
                    default => null,
                },
            ),

            'password' => Hash::make(password(
                label: 'Password',
                required: true,
            )),
        ];
    }

    public function createUser()
    {
        $user = User::create($this->getUserData());

        $user->assignRole('super_admin');

        return $user;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // check if there are any roles in the database
        if (!Role::count()) {
            $this->error('No roles found in the database. Please run php artisan shield:install first.');
            return;
        }

        $user = $this->createUser();

        $this->info('Admin user created successfully.');
    }
}
