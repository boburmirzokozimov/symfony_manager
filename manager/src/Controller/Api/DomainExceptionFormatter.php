<?php

namespace App\Controller\Api;

use App\Controller\ErrorHandler;
use DomainException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class DomainExceptionFormatter implements EventSubscriberInterface
{

    public function __construct(private ErrorHandler $errors)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return ([
            KernelEvents::EXCEPTION => 'onKernelException',
        ]);
    }

    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();
        $request = $event->getRequest();

        if (!$exception instanceof DomainException) {
            return;
        }

        if (strpos($request->attributes->get('_route'), 'api.') !== 0) {
            return;
        }

        $this->errors->handle($exception);

        $event->setResponse(new JsonResponse(
            [
                'error' => [
                    'code' => 400,
                    'message' => $exception->getMessage(),
                ]
            ], 400
        ));
    }
}