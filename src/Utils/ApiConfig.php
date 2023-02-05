<?php
namespace Wintex\SimpleApiBundle\Utils;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ApiConfig
{
    const PARAM_PREFIX = "wintex_simple_api";

    private string $entity_namespace;
    private array $entity_definitions;

    public function __construct(ParameterBagInterface $params) {
        $this->entity_namespace = $params->get(self::PARAM_PREFIX . ".entity_namespace");
        $this->entity_definitions = $params->get(self::PARAM_PREFIX . ".entity_definitions");
    }

    public function getGroups($entityClass, ?string $routeName) : array | null
    {
        return $this->entity_definitions[$entityClass]['routes'][$routeName]['groups'] ?? $this->entity_definitions[$entityClass]['groups'] ?? null;
    }
    
    public function getExtractProperties($entityClass, ?string $routeName)
    {
        return $this->entity_definitions[$entityClass]['routes'][$routeName]['extract_property'] ?? $this->entity_definitions[$entityClass]['extract_property'] ?? null;
    }

    public function getEntityNamespace()
    {
        return $this->entity_namespace;
    }
}