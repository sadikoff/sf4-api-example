<?php

namespace App\Listener;

use Symfony\Component\HttpFoundation as HTTP;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ExceptionListener
{
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        $response  = new HTTP\JsonResponse();

        if ($exception instanceof HttpExceptionInterface) {
            $code = $exception->getStatusCode();
            $response->headers->replace($exception->getHeaders());
        } else {
            $code = HTTP\Response::HTTP_INTERNAL_SERVER_ERROR;
        }

        $data = [
            'message' => $exception->getMessage(),
//            'trace'   => $exception->getTrace(),
        ];

        $response->setStatusCode($code);
        $response->setData($data);
        $event->setResponse($response);
    }
}