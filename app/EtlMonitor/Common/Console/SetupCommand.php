<?php

namespace App\EtlMonitor\Common\Console;

use App\EtlMonitor\Common\Enum\PermissionEnum;
use App\EtlMonitor\Common\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class SetupCommand extends Command
{

    protected $signature = 'etl_monitor:setup {--skip-auth} {--skip-queues}';

    protected $description = 'Initial Setup';

    public function handle()
    {
        echo '# Generating app key ...' . PHP_EOL;
        Artisan::call('key:generate');

        if (!$this->option('skip-auth')) {
            echo '# Setting up authentication provider ...' . PHP_EOL;
            Artisan::call('vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"');
            Artisan::call('jetstream:install livewire');
        }

        if (!$this->option('skip-queues')) {
            echo '# Setting up queues ...' . PHP_EOL;
            Artisan::call('queue:table');
        }

        echo '# Migrating database ...' . PHP_EOL;
        Artisan::call('migrate');

        echo '# Setting up defaults ...' . PHP_EOL;
        $defaults = [
            'Sla' => [
                'sla_definition_stati'
            ]
        ];

        foreach ($defaults as $namespace => $files) {
            foreach ($files as $file) {
                $default = include __DIR__ . '/../../' . $namespace . '/Database/default/' . $file . '.php';
                echo "Creating " . $file . " ..." . PHP_EOL;
                $class = $default['model'];
                if (isset($default['items'])) {
                    foreach ($default['items'] as $item) {
                        $class::create($item);
                    }
                }

                if (isset($default['attach'])) {
                    foreach ($default['attach'] as $id => $relations) {
                        foreach ($relations as $attach) {
                            $model = $default['model']::find($id);
                            $relation = $attach['relation'];
                            $model->$relation()->attach($attach['id']);
                        }
                    }
                }
            }
        }

        $password = 'a'; #Str::random(10);
        $user = [
            'name' => 'Admin',
            'email' => 'a@a.aa', #' Str::random(6) . '@etl_monitor.io',
            'password' => bcrypt($password)
        ];

        /** @var User $superadmin */
        $superadmin = User::create($user);
        $superadmin->syncPermissions([
            'Api' => [PermissionEnum::PERMISSION_READ(), PermissionEnum::PERMISSION_WRITE(), PermissionEnum::PERMISSION_ADMIN()],
            'Common' => [PermissionEnum::PERMISSION_READ(), PermissionEnum::PERMISSION_WRITE(), PermissionEnum::PERMISSION_ADMIN()],
            'Sla' => [PermissionEnum::PERMISSION_READ(), PermissionEnum::PERMISSION_WRITE(), PermissionEnum::PERMISSION_ADMIN()]
        ]);

        echo "################################" . PHP_EOL;
        echo "######### Initial User #########" . PHP_EOL;
        echo "# Username: " . $user['email'] . " #" . PHP_EOL;
        echo "# Password: " . $password . " #########" . PHP_EOL;
        echo "# Token: " . $superadmin->createToken('test')->plainTextToken . PHP_EOL;
        echo "################################" . PHP_EOL;

        echo "Setup done" . PHP_EOL;
        echo "If you wish to seed demo data run: php artisan etl_monitor:seed" . PHP_EOL;
    }

}
