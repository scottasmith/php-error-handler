<?php

declare(strict_types=1);

namespace ScottSmith\ErrorHandler\Integration\Mezzio;

use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Stratigility\Utils;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ScottSmith\ErrorHandler\Exception\ApplicationException;
use ScottSmith\ErrorHandler\ExceptionFormatter;
use Throwable;

final class ErrorResponseGenerator
{
    public const TEMPLATE_DEFAULT = 'error::error';
    public const LAYOUT_DEFAULT = 'layout::default';

    /**
     * @var ErrorHandler
     */
    private ErrorHandler $errorHandler;

    private bool $isDebug;

    private string $template;

    private string $layout;

    /**
     * @var TemplateRendererInterface|null
     */
    private ?TemplateRendererInterface $renderer;

    /**
     * ErrorResponseGenerator constructor.
     * @param ErrorHandler $errorHandler
     * @param bool $isDebug
     * @param string $template
     * @param string $layout
     * @param TemplateRendererInterface|null $renderer
     */
    public function __construct(
        ErrorHandler $errorHandler,
        bool $isDebug = false,
        string $template = self::TEMPLATE_DEFAULT,
        string $layout = self::LAYOUT_DEFAULT,
        TemplateRendererInterface $renderer = null
    ) {
        $this->errorHandler = $errorHandler;
        $this->isDebug = $isDebug;
        $this->template = $template;
        $this->layout = $layout;
        $this->renderer = $renderer;
    }

    public function __invoke(
        Throwable $exception,
        ServerRequestInterface $request,
        ResponseInterface $response
    ): ResponseInterface {
        $identifier = $this->errorHandler->report($exception);

        $errorCode = ExceptionFormatter::getCode($exception);
        $message = ExceptionFormatter::getMessage($exception, $this->isDebug);

        $response = $response->withStatus(Utils::getStatusCode($exception, $response))
            ->withHeader('X-Identifier', $identifier)
            ->withHeader('X-Error-Code', $errorCode);

        if ($this->isDebug) {
            $response = $response->withHeader('X-Error', $message);
        }

        $responseString = null;

        if ($this->isXmlHttpRequest($request) || !$this->renderer) {
            return $this->createJsonResponse($exception, $identifier, $message, $errorCode, $response);
        } else {
            $responseString = $this->createTemplateResponse($exception, $identifier, $request, $response);
        }

        $response->getBody()->write($responseString);
        return $response;
    }

    private function isXmlHttpRequest(ServerRequestInterface $request): bool
    {
        return 'XMLHttpRequest' == $request->getHeaderLine('X-Requested-With');
    }

    private function createJsonResponse(
        Throwable $exception,
        string $identifier,
        string $message,
        int $errorCode,
        ResponseInterface $response
    ): JsonResponse {
        $jsonData = [
            'error' => [
                'message' => $message,
                'code' => $errorCode,
                'httpStatusCode' => $response->getStatusCode(),
                'identifier' => $identifier,
                'stacktrace' => ExceptionFormatter::getStackTrace($exception, $this->isDebug),
            ],
            'data' => null,
        ];

        if ($this->isDebug) {
            $jsonData['error']['meta'] = $exception->getMetaData();
        }

        return new JsonResponse($jsonData, $response->getStatusCode(), $response->getHeaders());
    }

    private function createTemplateResponse(
        Throwable $exception,
        string $identifier,
        ServerRequestInterface $request,
        ResponseInterface $response
    ): string {
        return $this->renderer->render(
            $this->template,
            [
                'identifier' => $identifier,
                'debug' => $this->isDebug,
                'uri' => (string)$request->getUri(),
                'status' => $response->getStatusCode(),
                'reason' => $response->getReasonPhrase(),
                'stacktrace' => ExceptionFormatter::getStackTrace($exception, $this->isDebug),
                'meta' => ($exception instanceof ApplicationException) ? $exception->getMetaData() : [],
                'request' => $request,
                'response' => $response,
                'layout' => $this->layout,
            ]
        );
    }
}
