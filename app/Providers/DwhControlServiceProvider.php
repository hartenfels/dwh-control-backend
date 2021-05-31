<?php

namespace App\Providers;

use App\DwhControl\Common\Console\SeedCommand;
use App\DwhControl\Common\Console\SetupCommand;
use App\DwhControl\Common\Models\User;
use App\DwhControl\Etl\Console\UpdateDefinitionsFromExecutionsCommand;
use App\DwhControl\Sla\Console\CalculateAffectingEtlsCommand;
use App\DwhControl\Sla\Console\CalculateCommand;
use App\DwhControl\Sla\Console\CreateCommand;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class DwhControlServiceProvider extends ServiceProvider
{

    /**
     * @var array
     */
    protected static array $packages = ['Api', 'Common', 'Etl', 'Sla', 'Web'];

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

    /**
     *
     */
    private function schedule()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                SetupCommand::class,
                SeedCommand::class,

                CreateCommand::class,
                CalculateCommand::class,
                CalculateAffectingEtlsCommand::class,

                UpdateDefinitionsFromExecutionsCommand::class
            ]);
        }
    }

    /**
     *
     */
    public function routes()
    {
        foreach (static::$packages as $package) {
            if (file_exists($f = __DIR__ . '/../DwhControl/' . $package . '/Http/routes.php')) {
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

    /**
     *
     */
    public function migrations()
    {
        foreach (static::$packages as $package) {
            if (file_exists($path = __DIR__ . '/../DwhControl/' . $package . '/Database/migrations')) {
                $this->loadMigrationsFrom($path);
            }
        }
    }

    /**
     *
     */
    public function translations()
    {
        foreach (static::$packages as $package) {
            if (file_exists($path = __DIR__ . '/../DwhControl/' . $package . '/resources/lang')) {
                $this->loadTranslationsFrom($path, 'etl_monitor.' . strtolower($package));
            }
        }
    }

}
