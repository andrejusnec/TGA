<?php

declare(strict_types=1);

namespace App\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class ApiExceptionListener
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof HttpExceptionInterface) {
            $response = new JsonResponse(
                [
                    'errors' => $this->formatErrorMessage($exception)
                ],
                $exception->getStatusCode()
            );

            $event->setResponse($response);
        }
    }

    private function formatErrorMessage(Throwable $exception): array
    {
        $decodedMessage = json_decode($exception->getMessage(), true);

        if (JSON_ERROR_NONE === json_last_error() && isset($decodedMessage['errors'])) {
            return $decodedMessage['errors'];
        }

        return [$exception->getMessage()];
    }
}
