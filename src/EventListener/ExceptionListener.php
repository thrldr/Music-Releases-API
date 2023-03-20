<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

/** All other unhandled exceptions wind up here */
class ExceptionListener
{
    public function onException(ExceptionEvent $event)
    {
        $response = new JsonResponse();
        $exception = $event->getThrowable();

        if ($exception instanceof HttpExceptionInterface) {
            $statusCode = $exception->getStatusCode();
        } else {
            $statusCode = $exception->getCode() ?? Response::HTTP_INTERNAL_SERVER_ERROR;
            $statusCode = $statusCode < 100 ? 500 : $statusCode;
        }

        $message = "Error " . $statusCode . ": " . $exception->getMessage();
        $response->setData(['message' => $message]);
        $response->setStatusCode($statusCode);
        $event->setResponse($response);
    }
}
