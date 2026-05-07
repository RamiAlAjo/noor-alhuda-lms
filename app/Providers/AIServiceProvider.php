<?php

namespace App\Providers;

use App\Services\AI\CapacityDataCollector;
use App\Services\AI\CapacityPredictionService;
use App\Services\AI\Engines\StudentPerformancePredictionEngine;
use App\Services\AI\FeatureEngineering;
use App\Services\AI\FeatureExtractors\StudentFeatureExtractor;
use App\Services\AI\Validators\PredictionValidator;
use Illuminate\Support\ServiceProvider;

/**
 * AI Service Provider
 *
 * Registers all AI/ML services in the service container.
 * Implements singleton pattern for efficient resource usage.
 */
class AIServiceProvider extends ServiceProvider
{
    /**
     * Register services in the container
     */
    public function register(): void
    {
        // Register feature extractors as singletons
        $this->app->singleton(StudentFeatureExtractor::class, function ($app) {
            return new StudentFeatureExtractor;
        });

        // Register validators as singletons
        $this->app->singleton(PredictionValidator::class, function ($app) {
            return new PredictionValidator;
        });

        // Register prediction engines as singletons
        $this->app->singleton(StudentPerformancePredictionEngine::class, function ($app) {
            return new StudentPerformancePredictionEngine(
                $app->make(StudentFeatureExtractor::class),
                $app->make(PredictionValidator::class)
            );
        });

        // Register existing capacity prediction services
        $this->app->singleton(FeatureEngineering::class, function ($app) {
            return new FeatureEngineering;
        });

        $this->app->singleton(CapacityDataCollector::class, function ($app) {
            return new CapacityDataCollector;
        });

        $this->app->singleton(CapacityPredictionService::class, function ($app) {
            return new CapacityPredictionService(
                $app->make(FeatureEngineering::class)
            );
        });

        // Bind interfaces to implementations
        $this->app->bind(
            \App\Services\AI\Contracts\PredictionInterface::class,
            StudentPerformancePredictionEngine::class
        );
    }

    /**
     * Bootstrap services after registration
     */
    public function boot(): void
    {
        // Publish configuration if needed
        if ($this->app->runningInConsole()) {
            $this->publishes([
                config_path('services.php') => config_path('services.php'),
            ], 'ai-config');
        }

        // Register AI log channel
        $this->app->make('log')->channel('ai');
    }
}
