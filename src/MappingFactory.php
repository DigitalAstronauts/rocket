<?php
declare(strict_types=1);

namespace Rocket;

use Rocket\Mapping\Id;
use Rocket\Mapping\Resource;
use Rocket\Mapping\SubResource;

class MappingFactory
{
    /**
     * @param string $resourcePath
     * @param string $namespace
     * @return iterable | ResourceCollection[]
     * @throws \ReflectionException
     */
    public function create(
        string $resourcePath,
        string $namespace
    ): iterable
    {
        $collections = [];
        foreach ($this->getResourceCollections($resourcePath, $namespace) as $collection) {
            $collections[] = $collection;
        }
        return $collections;
    }

    private function getReflectionIterator(string $resourcePath, string $namespace): array
    {
        $reflections = [];
        /** @var \SplFileInfo[] $iterator */
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($resourcePath, \RecursiveDirectoryIterator::SKIP_DOTS)
        );
        $resourceRealPath = realpath($resourcePath);
        foreach ($iterator as $item) {
            if (is_dir($item->getFilename())) continue;
            $classSuffix = trim(
                str_replace(
                    [$resourceRealPath, '.php', '/'],
                    ['', '', '\\'],
                    $item->getRealPath()
                ),
                '\\'
            );
            $className = sprintf('%s\\%s', $namespace, $classSuffix);
            $reflections[] = new \ReflectionClass($className);
        }
        return $reflections;
    }

    private function getResourceCollections(string $resourcePath, string $namespace): array
    {
        $collections = [];
        foreach ($this->getReflectionIterator($resourcePath, $namespace) as $reflection) {
            $collection = new ResourceCollection();
            $parentPath = null;
            foreach ($reflection->getAttributes() as $attribute) {
                $mapping = $attribute->newInstance();
                if ($mapping instanceof Resource) {
                    $mapping->resourceClass = $reflection->getName();
                    $parentPath = $mapping->path;
                    $collection->addResource($mapping);
                }
                // search sub-resources and id
                foreach ($reflection->getProperties() as $property) {
                    foreach ($property->getAttributes() as $propertyAttribute) {
                        $mapping = $propertyAttribute->newInstance();
                        if($mapping instanceof Id) {
                            $collection->setId($mapping);
                        }
                        if($mapping instanceof SubResource) {
                            $mapping->parentResource = $reflection->getName();
                            $mapping->type = $property->getType()->getName();
                            $mapping->parentPath = $parentPath;
                            $collection->addSubResource($mapping);
                        }
                    }
                }
            }
            $collections[] = $collection;
        }
        return $collections;
    }
}
