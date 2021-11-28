<?php
declare(strict_types=1);

namespace Rocket\Mapping;

#[\Attribute]
class Id
{
    public function __construct(
        public string $name = 'id'
    )
    {
    }
}
