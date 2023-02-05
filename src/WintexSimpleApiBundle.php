<?php
namespace Wintex\SimpleApiBundle;

use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use Wintex\SimpleApiBundle\DependecyInjection\WintexSimpleApiExtension;

class WintexSimpleApiBundle extends AbstractBundle
{
    public function getContainerExtension() : ?ExtensionInterface
    {
        return new WintexSimpleApiExtension;
    }
}