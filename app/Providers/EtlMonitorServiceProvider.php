<?php

namespace App\Providers;

use App\EtlMonitor\Common\Console\SeedCommand;
use App\EtlMonitor\Common\Console\SetupCommand;
use App\EtlMonitor\Common\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class EtlMonitorServiceProvider extends ServiceProvider
{

    /**
     * @var array
     */
    protected static array $packages = ['Api', 'Common', 'Sla', 'Web'];

    /**
     * @return void
     */
    public function boot()
    {
        $this->schedule();
        $this->migrations();
        $this->routes();
        $this->authorize();
        $this->translations();
    }

    private function schedule()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                SetupCommand::class,
                SeedCommand::class
            ]);
        }
    }

    /**
     *
     */
    public function routes()
    {
        foreach (static::$packages as $package) {
            if (file_exists($f = __DIR__ . '/../EtlMonitor/' . $package . '/Http/routes.php')) {
                $this->loadRoutesFrom($f);
            }
        }
    }

    /**
     *
     */
    public function authorize()
    {
        $types = ['read', 'write', 'admin'];

        foreach (static::$packages as $package) {
            foreach ($types as $type) {
                Gate::define($type . '-' . $package, function (User $user) use ($package, $type) {
                    return $user->hasPermission($package, $type);
                });
            }
        }
    }

    public function migrations()
    {
        foreach (static::$packages as $package) {
            if (file_exists($path = __DIR__ . '/../EtlMonitor/' . $package . '/Database/migrations')) {
                $this->loadMigrationsFrom($path);
            }
        }
    }

    public function translations()
    {
        foreach (static::$packages as $package) {
            if (file_exists($path = __DIR__ . '/../EtlMonitor/' . $package . '/resources/lang')) {
                $this->loadTranslationsFrom($path, 'etl_monitor.' . strtolower($package));
            }
        }
    }

}
