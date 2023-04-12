<?php

namespace WebFuelAgency\UserAuthentication\Console;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\File;

trait InstallsApiStack
{
    /**
     * Install the API Breeze stack.
     *
     * @return int|null
     */
    protected function installApiStack()
    {
        $files = new Filesystem;

        //Handler
        $files->exists(app_path('Exceptions/Handler.php'));
        $files->copy(__DIR__.'/../../stubs/api/app/Exceptions/Handler.php', app_path('Exceptions/Handler.php'));

        // Controllers...
        $files->ensureDirectoryExists(app_path('Http/Controllers/Api/V1/Auth'));
        $files->copyDirectory(__DIR__.'/../../stubs/api/app/Http/Controllers/Api/V1/Auth', app_path('Http/Controllers/Api/V1/Auth'));

        $files->exists(app_path('Http/Controllers/Api/V1/ProfileApiController.php'));
        $files->copy(__DIR__.'/../../stubs/api/app/Http/Controllers/Api/V1/ProfileApiController.php', app_path('Http/Controllers/Api/V1/ProfileApiController.php'));

        // Middleware...
        $files->copyDirectory(__DIR__.'/../../stubs/api/app/Http/Middleware', app_path('Http/Middleware'));

        $this->replaceInFile('// \Laravel\Sanctum\Http', '\Laravel\Sanctum\Http', app_path('Http/Kernel.php'));

        $this->replaceInFile(
            '\Illuminate\Auth\Middleware\EnsureEmailIsVerified::class',
            '\App\Http\Middleware\EnsureEmailIsVerified::class',
            app_path('Http/Kernel.php')
        );

        // Requests...
        $files->ensureDirectoryExists(app_path('Http/Requests/Api/Auth'));
        $files->copyDirectory(__DIR__.'/../../stubs/api/app/Http/Requests/Api/Auth', app_path('Http/Requests/Api/Auth'));

        $files->ensureDirectoryExists(app_path('Http/Requests/Api/Profile'));
        $files->copyDirectory(__DIR__.'/../../stubs/api/app/Http/Requests/Api/Profile', app_path('Http/Requests/Api/Profile'));

        // Resources...
        $files->exists(app_path('Http/Resources/UserResource.php'));
        $files->copyDirectory(__DIR__.'/../../stubs/api/app/Http/Resources', app_path('Http/Resources'));

        // Models
        $this->addHasApiTokensTrait();
        $this->addSetPasswordAttribute();

        // Providers...
        $files->copyDirectory(__DIR__.'/../../stubs/api/app/Providers', app_path('Providers'));
        $this->replaceInFile("HOME = '/home'", "HOME = '/dashboard'", app_path('Providers/RouteServiceProvider.php'));

        //lang
        $files->ensureDirectoryExists(lang_path('en'));
        $files->copyDirectory(__DIR__.'/../../stubs/api/lang/en', lang_path('en'));

        // Routes...
        copy(__DIR__.'/../../stubs/api/routes/api.php', base_path('routes/api.php'));
        copy(__DIR__.'/../../stubs/api/routes/auth.php', base_path('routes/auth.php'));

        // Configuration...
        $files->copyDirectory(__DIR__.'/../../stubs/api/config', config_path());

        $this->replaceInFile(
            "'url' => env('APP_URL', 'http://localhost')",
            "'url' => env('APP_URL', 'http://localhost'),".PHP_EOL.PHP_EOL."    'frontend_url' => env('FRONTEND_URL', 'http://localhost:3000')",
            config_path('app.php')
        );

        // Environment...
        if (! $files->exists(base_path('.env'))) {
            copy(base_path('.env.example'), base_path('.env'));
        }

        file_put_contents(
            base_path('.env'),
            preg_replace('/APP_URL=(.*)/', 'APP_URL=http://localhost:8000'.PHP_EOL.'FRONTEND_URL=http://localhost:3000', file_get_contents(base_path('.env')))
        );

        // Tests...
        if (! $this->installTests()) {
            return 1;
        }

        $this->components->info('Webfuelagency scaffolding installed successfully.');
    }

    protected function addHasApiTokensTrait()
    {
        $path = app_path('Models/User.php');

        if (! file_exists($path)) {
            return;
        }

        $contents = file_get_contents($path);

        if (strpos($contents, 'HasApiTokens') !== false) {
            return;
        }

        $useStatements = "use Illuminate\Database\Eloquent\Factories\HasFactory;\nuse Laravel\Sanctum\HasApiTokens;";

        $contents = str_replace('use Illuminate\Database\Eloquent\Factories\HasFactory;', $useStatements, $contents);
        $contents = str_replace('use HasFactory,', 'use HasFactory, HasApiTokens,', $contents);

        file_put_contents($path, $contents);
    }

    protected function addSetPasswordAttribute()
    {
        $path = app_path('Models/User.php');

        if (!file_exists($path)) {
            return;
        }

        $contents = file_get_contents($path);

        if (strpos($contents, 'setPasswordAttribute') !== false) {
            return;
        }

        // Add namespace for Hash
        $contents = str_replace(
            'use Illuminate\Database\Eloquent\Factories\HasFactory;',
            "use Illuminate\Database\Eloquent\Factories\HasFactory;\nuse Illuminate\Support\Facades\Hash;",
            $contents
        );

        // Find last closing brace in User model
        $last_brace_position = strrpos($contents, '}');

        if ($last_brace_position === false) {
            return;
        }

        $function = <<<FUNC
            \n\tpublic function setPasswordAttribute(\$input)
            \t{
                \tif (\$input) {
                    \t\$this->attributes['password'] = Hash::needsRehash(\$input) ? Hash::make(\$input) : \$input;
                \t}
            \t}
            FUNC;

        // Insert function before the last closing brace
        $contents = substr_replace($contents, $function . "\n", $last_brace_position, 0);

        file_put_contents($path, $contents);
    }
}