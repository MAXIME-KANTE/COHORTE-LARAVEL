<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Force Laravel à lever une erreur si on oublie de précharger une relation (N+1)
        Model::preventLazyLoading(!app()->isProduction());
    }
}
