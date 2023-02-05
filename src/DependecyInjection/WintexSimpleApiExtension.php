<?php
namespace Wintex\SimpleApiBundle\DependecyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Wintex\SimpleApiBundle\Utils\ApiConfig;

class WintexSimpleApiExtension extends Extension
{
	/**
	 * Loads a specific configuration.
	 *
	 * @param array<array> $configs
	 * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
	 * @return mixed
	 */
	
	public function load(array $configs, \Symfony\Component\DependencyInjection\ContainerBuilder $container) 
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yaml');

		$configuration = new Configuration();
		$config = $this->processConfiguration($configuration, $configs);

		$container->setParameter(ApiConfig::PARAM_PREFIX . '.entity_namespace', $config['entity_namespace']);
		$container->setParameter(ApiConfig::PARAM_PREFIX . '.entity_definitions', $config['entities']);
    }
}