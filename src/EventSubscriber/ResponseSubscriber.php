<?php
namespace Wintex\SimpleApiBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\Serializer\Context\Normalizer\ObjectNormalizerContextBuilder;
use Symfony\Component\Serializer\SerializerInterface;
use Wintex\SimpleApiBundle\Serializer\EntityNormalizer;
use Wintex\SimpleApiBundle\Utils\ApiConfig;
use Wintex\SimpleApiBundle\Utils\ApiUtils;

class ResponseSubscriber implements EventSubscriberInterface
{
    private ApiConfig $config;
    private SerializerInterface $serializer;

    public function __construct(ApiConfig $config, SerializerInterface $serializer)
    {
        $this->config = $config;
        $this->serializer = $serializer;
    }

    public function serializeJson(ViewEvent $event)
    {
        if (!ApiUtils::isApiRoute($event->getRequest()))
            return;

        $method = $event->getRequest()->attributes->get('method') ?? null;
        $entityName = $event->getRequest()->attributes->get('entityClass');

        $groups = $this->config->getGroups($entityName, $method);
        $ctxBuilder = new ObjectNormalizerContextBuilder;
        
        $ctx = array_merge(
            $ctxBuilder->withGroups($groups)->toArray(),
            [EntityNormalizer::EXTRACT_PROPERTY => $this->config->getExtractProperties($entityName, $method)]
        );

        $data = $this->serializer->serialize($event->getControllerResult(), 'json', $ctx);
        
        $event->setResponse(new JsonResponse($data, json: true));
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ViewEvent::class => ['serializeJson', 10],
        ];
    }
}