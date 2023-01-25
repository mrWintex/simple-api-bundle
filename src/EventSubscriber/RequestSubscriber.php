<?php
namespace Wintex\SimpleApiBundle\EventSubscriber;

use Wintex\SimpleApiBundle\Service\ApiEndpointValidator;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

class RequestSubscriber implements EventSubscriberInterface
{
    private ApiEndpointValidator $apiValidator;

    public function __construct(ApiEndpointValidator $apiValidator)
    {
        $this->apiValidator = $apiValidator;
    }

    public function resolveApiEntity(RequestEvent $event)
    {
        if (!preg_match('/\/api\//', $event->getRequest()))
            return;
        
        if (!$event->getRequest()->attributes->has("entity"))
            return;

        $entityName = $event->getRequest()->attributes->get("entity");
        $entityClassName = "App\\Entity\\" . str_replace(' ', '', ucwords(strtolower(str_replace('-', ' ', $entityName))));

        if(!\class_exists($entityClassName)) {
            throw new HttpException(404, "entity {$entityName} not found!");
        }

        $this->apiValidator->supports($entityClassName, $event->getRequest());
            
        $event->getRequest()->attributes->set("entityClass", $entityClassName);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            RequestEvent::class => ['resolveApiEntity', 10],
        ];
    }
}