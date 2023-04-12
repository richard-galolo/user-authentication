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
        $files->copyDirectory(__DIR__.'/../../stubs/web/app/Http/Controllers/Admin/', app_path('Http/Controllers/Admin/'));

        $files->exists(app_path('Http/Controllers/Auth/LoginController.php'));
        $files->copyDirectory(__DIR__.'/../../stubs/web/app/Http/Controllers/Auth/', app_path('Http/Controllers/Auth/'));

        // Middleware...
        $files->copyDirectory(__DIR__.'/../../stubs/web/app/Http/Middleware', app_path('Http/Middleware'));

        // views...
        $files->exists(resource_path('views/welcome.blade.php'));
        $files->copyDirectory(__DIR__.'/../../stubs/web/resources/views/', resource_path('views/'));

        // web.php
        //TODO: refactor
        $path = base_path('routes/web.php');
        $content = file_get_contents($path);

        if (strpos($content, "Route::get('/', [HomeController::class, 'index'])->name('home');") !== false) {
            return;
        }

        $newContent = "\nRoute::group(['prefix' => '/', 'middleware' => ['auth', 'verified']], function () {\n\tRoute::get('/', [HomeController::class, 'index'])->name('home');\n});\n";
        file_put_contents($path, $content . $newContent);
    }
}