<?php

declare(strict_types=1);

namespace ScottSmith\ErrorHandler\Integration\Laravel;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use ScottSmith\ErrorHandler\ExceptionFormatter;
use ScottSmith\ErrorHandler\Identifier;
use ScottSmith\ErrorHandler\Reporter\Interfaces\ReporterInterface;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * @var string|null;
     */
    private ?string $identifier = null;

    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param Throwable $throwable
     * @return void
     *
     * @throws Throwable
     */
    public function report(Throwable $throwable)
    {
        $this->identifier = Identifier::getIdentifier();

        if ($this->container->has(ReporterInterface::class)) {
            /** @var ReporterInterface $reporter */
            $reporter = $this->container->get(ReporterInterface::class);
            $reporter->report($this->identifier, $throwable);
        }

        parent::report($throwable);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param Request $request
     * @param Throwable $throwable
     * @return Response
     *
     * @throws Throwable
     */
    public function render($request, Throwable $throwable)
    {
        $isDebug = (bool) env('APP_DEBUG', false);
        $errorCode = ExceptionFormatter::getCode($throwable);
        $message = ExceptionFormatter::getMessage($throwable, $isDebug);

        $response = parent::render($request, $throwable);
        $response->headers->set('X-Identifier', $this->identifier);
        $response->headers->set('X-Error-Code', $errorCode);

        if ($isDebug) {
            $response->headers->set('X-Error', $message);
        }

        $validationException = $throwable instanceof ValidationException
            ? $throwable->errors()
            : null;

        if ($request->wantsJson()) {
            $responseData = [
                'error' => [
                    'message'        => $message,
                    'code'           => $errorCode,
                    'httpStatusCode' => $response->getStatusCode(),
                    'identifier'     => $this->identifier,
                    'fields'         => $validationException,
                    'stacktrace'     => ExceptionFormatter::getStackTrace($throwable, $isDebug),
                ],
                'data' => null,
            ];

            return new JsonResponse($responseData, $response->getStatusCode(), $response->headers->all());
        }

        return $response;
    }
}
