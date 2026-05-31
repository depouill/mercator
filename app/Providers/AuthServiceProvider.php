<?php

namespace App\Providers;

use App\Models\Cartographer;
use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // 🧠 Hook global appelé AVANT toutes les autres règles Gate
        Gate::before(function ($user, string $ability) {
            // Si pas d'utilisateur (guest), on ne fait rien
            if (!$user) {
                return null;
            }

            // Récupère la liste des permissions mise en session au login
            $permissions = session('auth_permissions', []);

            // Si la permission demandée est dans la liste → autorisé direct
            if (in_array($ability, $permissions, true)) {
                return true;
            }

            // Sinon, on laisse les autres Gate::define() (ou policies) faire leur travail
            return null;
        });

        Gate::define('edit-object', function (User $user, \Illuminate\Database\Eloquent\Model $object) {
            $ability = Str::snake(class_basename($object)) . '_edit';

            // Session-based check (set at login)
            if (in_array($ability, session('auth_permissions', []), true)) {
                return true;
            }
            // Role-based gate fallback (covers API / test context where session is absent)
            if (Gate::forUser($user)->check($ability)) {
                return true;
            }

            return Cartographer::isAllowed($user, $object);
        });

        Gate::define('show-object', function (User $user, \Illuminate\Database\Eloquent\Model $object) {
            $ability = Str::snake(class_basename($object)) . '_show';

            // Session-based check (set at login)
            if (in_array($ability, session('auth_permissions', []), true)) {
                return true;
            }
            // Role-based gate fallback (covers API / test context where session is absent)
            if (Gate::forUser($user)->check($ability)) {
                return true;
            }

            return Cartographer::isAllowed($user, $object);
        });
    }
}
