<?php
declare(strict_types=1);

namespace Rocket\Mapping;

#[\Attribute(\Attribute::IS_REPEATABLE | \Attribute::TARGET_ALL)]
class SubResource extends Resource
{
    public string $parentResource;
    public string $parentPath;
    public string $type;
}
