# Rocket

This project should make building API easy as possible. We are using attribute mapping to entities to build easily structure that can help you implement your application router within few minutes.

## Usage

Let's imagine that we have some simple entities which represents our domain:

```php
class Author {  
    public int $id;
    public string $name;    
    public iterable $books;  
}

class Book {
    public int $id;
    public string $name;
    public string $isbn;
}
```

We want to build easy routing for this entities. With `Rocket` you should enrich those entities into:

```php
#[\Rocket\Mapping\Resource(path: "/authors")]
class Author {  
    public int $id;
    public string $name;
    #[\Rocket\Mapping\SubResource(path: "books")]    
    public iterable $books;  
}
#[\Rocket\Mapping\Resource(path: "books")]
class Book {
    public int $id;
    public string $name;
    public string $isbn;
}
$resourceCollection = (new \Rocket\MappingFactory)->create(
    '<dir> of entities/resources',
    'class namespace prefix'
);
```

With this definition you can use `MappingFactory` to get resource collection.

```php
$resourceCollection = (new \Rocket\MappingFactory)->create(
    'directory of entities/resources',
    'class namespace prefix - e.g. \App\Entity'
);
```

With this collection you can easily build your API router. I will show you usage with `league/route` that is used for [integration testing](./tests/MappingFactoryTest.php).

```php
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\ServerRequest;
use League\Route\Router;
use Rocket\MappingFactory;

function createRouter(): Router {
    $factory = new MappingFactory();
    $resourceCollections = $factory->create(
        __DIR__ . '/Fixture/Resource',
        'Rocket\Tests\Fixture\Resource'
    );

    $router = new Router();
    $handler = fn(ServerRequest $request) => new JsonResponse(['ok' => true]);
    foreach ($resourceCollections as $collection) {
        foreach ($collection->getResourceList() as $resource) {
            $route = $router->map(
                $resource->method,
                $resource->path,
                $handler
            );
            foreach ($resource->middlewares as $middleware) {
                $route->middleware(new $middleware());
            }
        }
        foreach ($collection->getSubResourceList() as $subResource) {
            $route = $router->map(
                $subResource->method,
                sprintf('%s/{%s}/%s', $subResource->parentPath, $collection->getId()->name, $subResource->path),
                $handler
            );
            foreach ($subResource->middlewares as $middleware) {
                $route->middleware(new $middleware());
            }
        }
    }
    return $router;
}
```
