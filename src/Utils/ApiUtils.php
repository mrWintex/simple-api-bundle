<?php
namespace Wintex\SimpleApiBundle\Utils;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Wintex\SimpleApiBundle\Annotations\ApiEndpoint;

class ApiUtils
{
    public static function isApiRoute(Request $request) : bool
    {
        return !preg_match('/\/wintex_api\//', $request->attributes->get("_route")) && $request->attributes->has("entity");
    }

    public static function supports(string $entityClassName, Request $request)
    {
        $reflectionClass = new \ReflectionClass($entityClassName);

        $apiAttributes = $reflectionClass->getAttributes(ApiEndpoint::class);
        $currentRoute = self::getCurrentRoute($request->get('_route'));

        foreach($apiAttributes as $attribute) {
            $supportTypes = $attribute->getArguments();

            if (in_array(ApiEndpoint::SUPPORT_ALL, $supportTypes) || in_array($currentRoute, $supportTypes)) {
                return;
            }
        }

        throw new HttpException(501, "This method is not supported on this entity");
    }

    public static function getCurrentRoute(string $routeName)
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