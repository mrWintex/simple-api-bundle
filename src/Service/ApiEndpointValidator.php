<?php
namespace Wintex\SimpleApiBundle\Service;

use Wintex\SimpleApiBundle\Annotations\ApiEndpoint;
use Psr\Log\LoggerInterface;
use ReflectionClass;
use SebastianBergmann\Template\RuntimeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use TheSeer\Tokenizer\Exception;

class ApiEndpointValidator
{
    public function __construct(private LoggerInterface $logger){}
    public function supports(string $entityClassName, Request $request)
    {
        $reflectionClass = new ReflectionClass($entityClassName);

        $apiAttributes = $reflectionClass->getAttributes(ApiEndpoint::class);
        $currentRoute = $this->getCurrentRoute($request->get('_route'));

        foreach($apiAttributes as $attribute) {
            $supportTypes = $attribute->getArguments();
            
            foreach ($supportTypes as $supportType)
                $this->logger->info($supportType);

            if (in_array(ApiEndpoint::SUPPORT_ALL, $supportTypes) || in_array($currentRoute, $supportTypes)) {
                return;
            }
        }

        throw new HttpException(501, "This method is not supported on this entity");
    }

    public function getCurrentRoute(string $routeName)
    {
        if ($routeName == null)
            throw new \Exception("RouteName not resolved!");
            
        if (str_ends_with($routeName, "_get")) {
            return ApiEndpoint::GET_ALL;
        } else if (str_ends_with($routeName, "_get_one")) {
            return ApiEndpoint::GET_ONE;
        } else if (str_ends_with($routeName, "_create")) {
            return ApiEndpoint::CREATE;
        } else if (str_ends_with($routeName, "_delete")) {
            return ApiEndpoint::DELETE;
        }
        return -1;
    }
}