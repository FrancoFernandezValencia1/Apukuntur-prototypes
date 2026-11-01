<?php

namespace App\Providers;

use App\Models\Usuario;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::define('admin', fn (Usuario $usuario) => $usuario->isAdmin());
        Gate::define('eu', fn (Usuario $usuario) => $usuario->isEu() || $usuario->isAdmin());
        Gate::define('fabrica', fn (Usuario $usuario) => $usuario->isFactory() || $usuario->isAdmin());
    }
}
