<?php

declare(strict_types=1);

namespace ScottSmith\ErrorHandler\Reporter;

use Bugsnag\BugsnagLaravel\Facades\Bugsnag;
use Bugsnag\Report;
use Throwable;

class LaravelBugsnagReporter extends AbstractReporter
{
    protected function vendorReport(string $identifier, Throwable $throwable, ?array $metadata = null)
    {
        Bugsnag::registerCallback(function (Report $report) use ($metadata) {
            $report->setMetaData($metadata);
        });
    }
}
