<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use App\Models\ChatConversation;
use App\Models\User;
use App\Models\WhatsappMessage;
use App\Models\Operator;
use App\Policies\ChatConversationPolicy;
use App\Policies\UserPolicy;
use App\Policies\RolePolicy;
use App\Policies\WhatsappMessagePolicy;
use App\Policies\OperatorPolicy;
use Spatie\Permission\Models\Role;

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
        // Configurar locale para intl
        if (extension_loaded('intl')) {
            ini_set('intl.default_locale', 'en_US');
        }

        // Registrar Observer para sincronizar Users → Operators
        User::observe(\App\Observers\UserObserver::class);

        // Registrar Policies para control de acceso
        Gate::policy(ChatConversation::class, ChatConversationPolicy::class);
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Role::class, RolePolicy::class);
        Gate::policy(WhatsappMessage::class, WhatsappMessagePolicy::class);
        Gate::policy(Operator::class, OperatorPolicy::class);
    }
}
