<?php
namespace Wintex\SimpleApiBundle\Annotations;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class ApiEndpoint
{
    public const SUPPORT_ALL = 0;
    public const GET_ALL = 1;
    public const GET_ONE = 2;
    public const CREATE = 3;
    public const DELETE = 4;

    public array $supportTypes;

    public function __construct(int ...$supportTypes)
    {
        $this->$supportTypes = $supportTypes;
    }
}