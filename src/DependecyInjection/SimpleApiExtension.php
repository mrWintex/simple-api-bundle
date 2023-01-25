<?php
namespace Wintex\SimpleApiBundle\DependecyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class SimpleApiExtension extends Extension
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
        $this->addAnnotatedClassesToCompile([
            'Wintex\\SimpleApiBundle\\Controller\\ApiController'
        ]);
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yaml');
    }
}