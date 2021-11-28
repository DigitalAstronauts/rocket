<?php
declare(strict_types=1);

namespace Rocket\Mapping;

#[\Attribute]
class Resource
{
    public function __construct(
        public string $path,
        public string $method = '*',
        public ?string $resourceClass = null,
        public ?string $handler = null,
        public array $middlewares = []
    )
    {
    }
}
