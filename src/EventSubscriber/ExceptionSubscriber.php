<?php
namespace Wintex\SimpleApiBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Wintex\SimpleApiBundle\Utils\ApiUtils;

class ExceptionSubscriber implements EventSubscriberInterface
{
    public function onException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();

        if (!($exception instanceof HttpException) || !ApiUtils::isApiRoute($event->getRequest()))
            return;

        $response = new JsonResponse([
            'message'       => $exception->getMessage(),
            'timestamp'     => time()
        ]);

        if ($exception instanceof HttpExceptionInterface) {
            $response->setStatusCode($exception->getStatusCode());
            $response->headers->replace($exception->getHeaders());
        } else {
            $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        
        $event->setResponse($response);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ExceptionEvent::class => ['onException', 10],
        ];
    }
}