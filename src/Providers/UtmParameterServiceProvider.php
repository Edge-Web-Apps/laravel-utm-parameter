<?php

namespace Suarez\UtmParameter\Providers;

use Illuminate\Support\Facades\Blade;
use Suarez\UtmParameter\UtmParameter;
use Illuminate\Support\ServiceProvider;

class UtmParameterServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(UtmParameter::class, fn () => new UtmParameter());
        $this->mergeConfigFrom(__DIR__.'/../config/utm-parameter.php', 'utm-parameter');
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([__DIR__.'/../config/utm-parameter.php' => config_path('utm-parameter.php')], 'utm-parameter');

        Blade::if('hasUtm', function (string $key, string|null $value = null) {
            return has_utm($key, $value);
        });

        Blade::if('hasNotUtm', function (string $key, string|null $value = null) {
            return has_not_utm($key, $value);
        });

        Blade::if('containsUtm', function (string $key, string|null $value = null) {
            return contains_utm($key, $value);
        });

        Blade::if('containsNotUtm', function (string $key, string|null $value = null) {
            return contains_not_utm($key, $value);
        });
    }
}
