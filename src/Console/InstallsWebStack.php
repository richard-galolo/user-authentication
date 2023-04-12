<?php

namespace WebFuelAgency\UserAuthentication\Console;

use Illuminate\Filesystem\Filesystem;

trait InstallsWebStack
{
    /**
     * Install the API Breeze stack.
     *
     * @return int|null
     */
    protected function InstallsWebStack()
    {
        $files = new Filesystem;

        // Kernel.php
        $this->replaceInFile(
            "'throttle' => \\Illuminate\\Routing\\Middleware\\ThrottleRequests::class,",
            "'throttle' => \\Illuminate\\Routing\\Middleware\\ThrottleRequests::class,\n\t\t'admin' => \\App\\Http\\Middleware\\AuthenticateAsAdmin::class,",
            app_path('Http/Kernel.php')
        );

        // Controllers...
        $files->exists(app_path('Http/Controllers/Admin/HomeController.php'));
        $files->copy(__DIR__.'/../../stubs/web/app/Http/Controllers/Admin/HomeController.php', app_path('Http/Controllers/Admin/HomeController.php'));

        $files->exists(app_path('Http/Controllers/Auth/LoginController.php'));
        $files->copy(__DIR__.'/../../stubs/web/app/Http/Controllers/Auth/LoginController.php', app_path('Http/Controllers/Auth/LoginController.php'));

        // Middleware...
        $files->copyDirectory(__DIR__.'/../../stubs/web/app/Http/Middleware', app_path('Http/Middleware'));

        // views...
        $files->exists(resource_path('views/welcome.blade.php'));
        $files->copy(__DIR__.'/../../stubs/web/resources/views/welcome.blade.php', resource_path('views/welcome.blade.php'));

        // web.php
        $newContent = "\nRoute::group(['prefix' => '/', 'middleware' => ['auth', 'verified']], function () {\n\tRoute::get('/', [HomeController::class, 'index'])->name('home');\n});\n";
        $this->replaceInFile(
            "Route::get('/', [HomeController::class, 'index'])->name('home');",
            $newContent,
            base_path('routes/web.php')
        );
    }
}