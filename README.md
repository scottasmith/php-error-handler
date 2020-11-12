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

The BugsnagReporter extends the AbstractReporter. This allows to extend the metadata:
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

The `app\Exceptions\Handler.php` needs to extend the `ScottSmith\ErrorHandler\Integrations\Laravel\Handler` class.

By default, the reporter to be used is the `ScottSmith\ErrorHandler\Reporter\NullReporter`.
To update the reported, simply update the published config `config\errror-handler.php`:
```
    'reporter' => \ScottSmith\ErrorHandler\Reporter\LaravelBugsnagReporter::class,
```
