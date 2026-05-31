<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use LdapRecord\Container;

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
        // Get version from file
        $versionFile = base_path('version.txt');
        $version = file_exists($versionFile) ? trim(file_get_contents($versionFile)) : '0.0.0';
        $this->app->instance('mercator.version', $version);

        // Startup log
        $this->logStartupInfo($version);

        // start Paginator
        Paginator::useBootstrap();

        // Enregistrer les vues avec un namespace
        $this->loadViewsFrom(resource_path('views'), 'mercator');

        // Force HTTPS
        $forceHttps = config('app.force_https');
        if ($forceHttps === true || ($forceHttps === null && App::environment('production'))) {
            URL::forceScheme('https');
        }

        if (config('app.db_trace') && ! $this->app->runningInConsole()) {
            DB::listen(function ($query): void {
                Log::info($query->time.':'.$query->sql);
            });
        }
        
        if (config('ldap.logging.enabled')) {
            Container::setLogger(
                Log::channel(config('ldap.logging.channel'))
            );
        }

        // Directives Blade cartographes
        Blade::directive('canEdit', function (string $expression) {
            return "<?php if(Gate::allows('edit-object', {$expression})): ?>";
        });
        Blade::directive('endcanEdit', function () {
            return "<?php endif; ?>";
        });

        Blade::directive('canShow', function (string $expression) {
            return "<?php if(Gate::allows('show-object', {$expression})): ?>";
        });
        Blade::directive('endcanShow', function () {
            return "<?php endif; ?>";
        });

        Blade::directive('canDelete', function (string $expression) {
            return "<?php if(Gate::allows(\\Illuminate\\Support\\Str::snake(class_basename({$expression}::class)) . '_delete')): ?>";
        });
        Blade::directive('endcanDelete', function () {
            return "<?php endif; ?>";
        });

        Blade::directive('canAccess', function (string $expression) {
            return "<?php if(\\App\\Models\\Cartographer::canAccess({$expression})): ?>";
        });
        Blade::directive('endcanAccess', function () {
            return "<?php endif; ?>";
        });

        Blade::directive('canAccessAny', function (string $expression) {
            return "<?php if(\\App\\Models\\Cartographer::canAccessAny([{$expression}])): ?>";
        });
        Blade::directive('endcanAccessAny', function () {
            return "<?php endif; ?>";
        });

        RateLimiter::for('api', function (Request $request) {
            if ($request->user()?->isAdmin()) {
                return Limit::none();
            }

            $limit = (int) config('api.rate_limit', 60);
            $decay = (int) config('api.rate_limit_decay', 1);

            return Limit::perMinutes($decay, $limit)
                ->by($request->user()?->id ?: $request->ip());
        });
    }

    private function logStartupInfo(string $version): void
    {
        if ($this->app->runningInConsole()) {
            // En console, ne logger que pour les commandes de démarrage serveur
            // (serve, octane:start, octane:reload) ou si startup_log est forcé à true
            if (! $this->isServerStartupCommand() && ! config('app.startup_log', false)) {
                return;
            }
        } elseif (! config('app.startup_log', false)) {
            return;
        }

        $dbDriver   = config('database.default');
        $dbVersion  = $this->getDatabaseVersion();
        $env        = App::environment();
        $debug      = config('app.debug');

        $forceHttps = config('app.force_https');
        $httpsMode  = match (true) {
            $forceHttps === true  => 'always',
            $forceHttps === false => 'never',
            default               => 'production-only',
        };

        // ---------------------------------------------------------------
        //  Structured JSON (pour ingestion ELK / Loki / Datadog)
        // ---------------------------------------------------------------
        Log::info('Mercator startup', [
            'mercator_version' => $version,
            'environment'      => $env,
            'debug'            => $debug ? 'enabled' : 'disabled',
            'url'              => config('app.url'),
            'php_version'      => PHP_VERSION,
            'laravel_version'  => app()->version(),
            'db_driver'        => $dbDriver,
            'db_version'       => $dbVersion,
            'db_trace'         => config('app.db_trace') ? 'enabled' : 'disabled',
            'https_mode'       => $httpsMode,
            'ldap_enabled'     => config('ldap.enabled') ? 'yes' : 'no',
            'ldap_logging'     => config('ldap.logging.enabled') ? 'enabled' : 'disabled',
            'api_rate_limit'   => config('api.rate_limit', 60).'/'.config('api.rate_limit_decay', 1).'min',
        ]);

        // ---------------------------------------------------------------
        //  Banner
        // ---------------------------------------------------------------
        Log::info('');
        Log::info('  ███╗   ███╗███████╗██████╗  ██████╗ █████╗ ████████╗ ██████╗ ██████╗ ');
        Log::info('  ████╗ ████║██╔════╝██╔══██╗██╔════╝██╔══██╗╚══██╔══╝██╔═══██╗██╔══██╗');
        Log::info('  ██╔████╔██║█████╗  ██████╔╝██║     ███████║   ██║   ██║   ██║██████╔╝');
        Log::info('  ██║╚██╔╝██║██╔══╝  ██╔══██╗██║     ██╔══██║   ██║   ██║   ██║██╔══██╗');
        Log::info('  ██║ ╚═╝ ██║███████╗██║  ██║╚██████╗██║  ██║   ██║   ╚██████╔╝██║  ██║');
        Log::info('  ╚═╝     ╚═╝╚══════╝╚═╝  ╚═╝ ╚═════╝╚═╝  ╚═╝   ╚═╝    ╚═════╝ ╚═╝  ╚═╝');
        Log::info('');
        Log::info("  Version {$version}  |  Laravel ".app()->version()."  |  PHP ".PHP_VERSION);
        Log::info('');

        // ---------------------------------------------------------------
        //  Section: Environment
        // ---------------------------------------------------------------
        Log::info('  ┌─ Environment ──────────────────────────────────────────────────┐');
        Log::info("  │  ENV   : {$env}");
        Log::info("  │  URL   : ".config('app.url'));
        Log::info("  │  DEBUG : ".($debug ? '⚠️  ENABLED — do not use in production' : 'disabled'));
        Log::info("  │  HTTPS : {$httpsMode}");
        Log::info('  └────────────────────────────────────────────────────────────────┘');

        // ---------------------------------------------------------------
        //  Section: Database
        // ---------------------------------------------------------------
        Log::info('  ┌─ Database ─────────────────────────────────────────────────────┐');
        Log::info("  │  DRIVER  : {$dbDriver}");
        Log::info("  │  VERSION : {$dbVersion}");
        Log::info("  │  TRACE   : ".(config('app.db_trace') ? 'enabled' : 'disabled'));
        Log::info('  └────────────────────────────────────────────────────────────────┘');

        // ---------------------------------------------------------------
        //  Section: Auth
        // ---------------------------------------------------------------
        Log::info('  ┌─ Auth ─────────────────────────────────────────────────────────┐');
        Log::info("  │  LDAP         : ".(config('ldap.enabled') ? 'enabled' : 'disabled'));
        Log::info("  │  LDAP LOGGING : ".(config('ldap.logging.enabled') ? 'enabled' : 'disabled'));
        Log::info('  └────────────────────────────────────────────────────────────────┘');

        // ---------------------------------------------------------------
        //  Section: API
        // ---------------------------------------------------------------
        Log::info('  ┌─ API ───────────────────────────────────────────────────────────┐');
        Log::info("  │  RATE LIMIT : ".config('api.rate_limit', 60).'/'.config('api.rate_limit_decay', 1).' min');
        Log::info('  └─────────────────────────────────────────────────────────────────┘');

        Log::info('');
        Log::info('  🚀 Mercator is ready.');
        Log::info('');

    }

    /**
     * Retrieve the database server version, gracefully.
     */
    private function getDatabaseVersion(): string
    {
        try {
            $pdo = DB::connection()->getPdo();

            return match (config('database.default')) {
                'mysql', 'mariadb' => $pdo->query('SELECT VERSION()')->fetchColumn(),
                'pgsql'            => $pdo->query('SHOW server_version')->fetchColumn(),
                'sqlite'           => 'SQLite '.$pdo->query('SELECT sqlite_version()')->fetchColumn(),
                default            => 'unknown',
            };
        } catch (\Throwable) {
            return 'unavailable';
        }
    }

    /**
     * Returns true only for artisan commands that actually start a server process.
     */
    private function isServerStartupCommand(): bool
    {
        $command = $_SERVER['argv'][1] ?? '';

        return in_array($command, [
            'serve',
            'octane:start',
            'octane:reload',
        ], strict: true);
    }
}