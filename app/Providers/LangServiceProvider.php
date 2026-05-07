<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class LangServiceProvider extends ServiceProvider
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
        // Register Blade directives for language
        Blade::directive('rtl', function () {
            return '<?php if(is_rtl()): ?>';
        });

        Blade::directive('endrtl', function () {
            return '<?php endif; ?>';
        });

        Blade::directive('l', function ($expression) {
            return "<?php echo __l($expression); ?>";
        });
    }
}
