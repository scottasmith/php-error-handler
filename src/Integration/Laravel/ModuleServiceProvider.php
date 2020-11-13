<?php

declare(strict_types=1);

namespace ScottSmith\ErrorHandler\Integration\Laravel;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\ServiceProvider;
use RuntimeException;
use ScottSmith\ErrorHandler\Reporter\NullReporter;
use ScottSmith\ErrorHandler\Reporter\Interfaces\ReporterInterface;

class ModuleServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/config.php', 'error-handler');

        $this->setupReporter();
    }

    /**
     * Application boot.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes(
            [
                __DIR__ . '/config.php' => config_path('error-handler.php'),
            ]
        );
    }

    /**
     * Setup the reporter with extra metadata
     */
    private function setupReporter(): void
    {
        $this->app->bind(ReporterInterface::class, fn () => $this->getReporter());
    }

    /**
     * @return ReporterInterface
     * @throws BindingResolutionException
     */
    private function getReporter(): ReporterInterface
    {
        $reporter = config('error-handler.reporter', NullReporter::class);

        if (!class_exists($reporter)) {
            throw new RuntimeException('reporter must be a valid class: ' . ReporterInterface::class);
        }

        $reporterClass = $this->app->make($reporter);
        if (!$reporterClass instanceof ReporterInterface) {
            throw new RuntimeException('reporter must be instance of ' . ReporterInterface::class);
        }

        return $reporterClass;
    }
}
