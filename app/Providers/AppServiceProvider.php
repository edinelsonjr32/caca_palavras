<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
// --- CORREÇÃO ESTÁ AQUI ---
// Certifique-se de que a linha abaixo está usando 'Facades', e não 'Contracts'.
use Illuminate\Support\Facades\View;
use App\Http\View\Composers\NavbarComposer;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Esta linha agora funcionará corretamente com a Facade importada.
        View::composer('partials.navbar', NavbarComposer::class);
    }
}
