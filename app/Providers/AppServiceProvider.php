<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Blade;

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
        Schema::defaultStringLength(191);

        // Register versioned asset Blade directive
        // The helper function is defined in app/helpers.php and autoloaded via composer.json
        Blade::directive('asset_version', function ($expression) {
            return "<?php echo asset_version({$expression}); ?>";
        });
    }
}
