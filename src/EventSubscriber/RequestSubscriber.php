<?php
namespace Wintex\SimpleApiBundle\EventSubscriber;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Wintex\SimpleApiBundle\Service\ApiEndpointValidator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Wintex\SimpleApiBundle\Utils\ApiConfig;
use Wintex\SimpleApiBundle\Utils\ApiUtils;

class RequestSubscriber implements EventSubscriberInterface
{
    private ApiConfig $config;

    public function __construct(ApiConfig $config)
    {
        $this->config = $config;
    }

    public function resolveApiEntity(RequestEvent $event)
    {
        if (!ApiUtils::isApiRoute($event->getRequest()))
            return;

        $entityName = $event->getRequest()->attributes->get("entity");
        $entityClassName = $this->config->getEntityNamespace() . str_replace(' ', '', ucwords(strtolower(str_replace('-', ' ', $entityName))));

        if(!\class_exists($entityClassName)) {
            throw new HttpException(404, "Entity {$entityName} not found!");
        }

        ApiUtils::supports($entityClassName, $event->getRequest());
            
        $event->getRequest()->attributes->set("entityClass", $entityClassName);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            RequestEvent::class => ['resolveApiEntity', 10],
        ];
    }
}