# PHP Error Reporter

## Installation
```
composer require scottsmith/error-reporter
```

## Configuration
There are two main things to set to make the reporter work.
- The reporter
- Use the base handler

### Reporter
There are two supported reporters:
- NullReporter
- LaravelBugsnagReporter

The BugsnagReporter extends the `AbstractReporter`. This allows to extend the metadata:
```
$reporter->registerMetaGenerator(new class implements MetaGeneratorInterface {
    public function generateMetaData(): array
    {
        return ['some' => 'data'];
    }
}
```

# Laravel Integration
## Configuration
You need to publish the configuration using `php artisan vendor:publish`.

This allows to update the reporter the ModuleServiceProvider binds as ReporterInterface. 

To use the bugsnag reporter you need to install and setup the `bugsnag/bugsnag-laravel` package.

The `app\Exceptions\Handler.php` needs to extend the `ScottSmith\ErrorHandler\Integration\Laravel\Handler` class.

By default, the reporter to be used is the `ScottSmith\ErrorHandler\Reporter\NullReporter`.
To update the reported, simply update the published config `config\error-handler.php`:
```
    'reporter' => \ScottSmith\ErrorHandler\Reporter\LaravelBugsnagReporter::class,
```

If the reporter extends `AbstractReporter` then you can extend the global data inside your Provider:
```
class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->extend(\ScottSmith\ErrorHandler\Reporter\AbstractReporter::class, function($reporter) {
            $reporter->registerMetaGenerator(new class implements MetaGeneratorInterface {
                public function generateMetaData(): array
                {
                    return ['some' => 'data'];
                }
            }
        });
    }
}
```

# Laminas/Mezzio Integration
## Configuration
If you are using the `laminas-component-installer` the module `ConfigProvider` should be added automatically.
If not then you will have to add the `ScottSmith\ErrorHandler\Integration\Mezzio\ConfigProvider` manually.
eg. config.php
```
$aggregator = new ConfigAggregator(
    [
        \ScottSmith\ErrorHandler\Integration\Laminas\ConfigProvider::class
        ...
    ]
);
```

You will then need to create configuration for the service manager. This README only covers the laminas service-manager.
Create a file named `config\autoload\error.global.php`
```
<?php
use Mezzio\Middleware\ErrorResponseGenerator;
use ScottSmith\ErrorHandler\Integration\Laminas\ErrorResponseGeneratorFactory;

return [
    'dependencies' => [
        'factories' => [
            ErrorResponseGenerator::class => ErrorResponseGeneratorFactory::class,
        ],
    ],
    
    'error_handler' => [
        // Is the application in debug mode
        'debug' => false,

        // Which reporter to use
        'reporter' => \ScottSmith\ErrorHandler\Reporter\LoggerReporter::class,
        
        // The templates to use if you have initialized templating with Mezzio\Template\TemplateRendererInterface 
        'template_404'   => 'error::404',
        'template_error' => 'error::error',
    ],
];
```

If the reporter extends `AbstractReporter` then you can extend the global data inside your Provider:
```
<?php
use Mezzio\Middleware\ErrorResponseGenerator;
use ScottSmith\ErrorHandler\Integration\Laminas\ErrorResponseGeneratorFactory;

return [
    'dependencies' => [
        'initializers' => [
            function(ContainerInterface $container, $instance) {
                if (!$instance instanceof \ScottSmith\ErrorHandler\Reporter\AbstractReporter) {
                    return;
                }
                
                $instance->registerMetaGenerator(new class implements MetaGeneratorInterface {
                    public function generateMetaData(): array
                    {
                        return ['some' => 'data'];
                    }
                }
            }
        ]
    ],
];
```
