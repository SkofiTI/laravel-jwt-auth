<?php

namespace Skofi\LaravelJwtAuth\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class InstallCommand extends Command
{
    protected $signature = 'jwt-auth:install';
    protected $description = 'Install the JWT authentication package';

    public function handle()
    {
        $files = new Filesystem;

        // Configuration
        $files->replaceInFile(
            "'url' => env('APP_URL', 'http://localhost')",
            "'url' => env('APP_URL', 'http://localhost'),".PHP_EOL.PHP_EOL."    'frontend_url' => env('FRONTEND_URL', 'http://localhost:3000')",
            config_path('app.php')
        );

        // Environment
        if (!$files->exists(base_path('.env'))) {
            copy(base_path('.env.example'), base_path('.env'));
        }

        file_put_contents(
            base_path('.env'),
            preg_replace('/APP_URL=(.*)/', 'APP_URL=http://localhost:8000'.PHP_EOL.'FRONTEND_URL=http://localhost:3000', file_get_contents(base_path('.env')))
        );

        // Routes
        copy(__DIR__.'/../routes/api.php', base_path('routes/api.php'));

        // Exceptions
        $files->copyDirectory(__DIR__.'/../app/Exceptions', app_path('Exceptions'));

        // Controllers
        $files->ensureDirectoryExists(app_path('Http/Controllers/Auth'));
        $files->copyDirectory(__DIR__.'/../app/Http/Controllers/Auth', app_path('Http/Controllers/Auth'));

        // Requests
        $files->ensureDirectoryExists(app_path('Http/Requests/Auth'));
        $files->copyDirectory(__DIR__.'/../app/Http/Requests/Auth', app_path('Http/Requests/Auth'));

        $this->info('Package installed successfully.');
    }
}
