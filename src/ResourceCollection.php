<?php
declare(strict_types=1);

namespace Rocket;

use Rocket\Mapping\Id;
use Rocket\Mapping\SubResource;
use Rocket\RouteMapping;
use Rocket\Mapping\Resource;

class ResourceCollection
{
    private array $resourceList = [];
    private array $subResourceList = [];
    private ?Id $id = null;

    public function addResource(Resource $resource)
    {
        $this->resourceList[$resource->path] = $resource;
    }

    public function setId(Id $id): void
    {
        $this->id = $id;
    }

    public function addSubResource(SubResource $subResource): void
    {
        $this->subResourceList[] = $subResource;
    }

    /**
     * @return array | Resource[]
     */
    public function getResourceList(): array
    {
        return $this->resourceList;
    }

    /**
     * @return array | SubResource[]
     */
    public function getSubResourceList(): array
    {
        return $this->subResourceList;
    }

    public function getId(): ?Id
    {
        return $this->id;
    }
}
