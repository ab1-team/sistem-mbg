<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Stancl\Tenancy\Database\Models\Domain;
use Stancl\Tenancy\Database\Models\Tenant;

class CreateYayasan extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'yayasan:create {name} {domain} {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Provision a new Yayasan (Foundation) with isolated database and SuperAdmin.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $domain = $this->argument('domain');
        $email = $this->argument('email');

        $this->info("Provisioning Yayasan: {$name}...");

        // 1. Create Tenant
        $tenant = Tenant::create([
            'id' => str($name)->slug()->toString(),
            'name' => $name,
        ]);

        // 2. Create Domain
        Domain::create([
            'domain' => $domain,
            'tenant_id' => $tenant->id,
        ]);

        $this->info("Database and Domain created. Initializing SuperAdmin in tenant context...");

        // 3. Create SuperAdmin in Tenant Context
        $tenant->run(function () use ($email, $name) {
            $user = User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => "Admin {$name}",
                    'password' => Hash::make('password'), // Direct password for first setup
                    'uuid' => (string) \Illuminate\Support\Str::uuid(),
                ]
            );

            // Assign Super Admin Role
            $user->assignRole('superadmin');
        });

        $this->info("Success! Yayasan {$name} is ready at http://{$domain}");
        $this->comment("Default Password: password (Please change immediately)");
    }
}
