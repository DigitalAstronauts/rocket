<?php
declare(strict_types=1);

namespace Rocket\Mapping;

#[\Attribute]
class SubResource extends Resource
{
    public string $parentResource;
    public string $parentPath;
    public string $type;
}
