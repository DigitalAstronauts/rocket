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
    #[\Rocket\Mapping\Id]
    #[\Rocket\Mapping\SubResource]
    public int $id;
    public string $name;
    #[\Rocket\Mapping\SubResource(path: "books")]    
    public iterable $books;  
}

#[
    \Rocket\Mapping\Resource(method: 'GET', path: "/books", handler: 'YourRetrieveController'),
    \Rocket\Mapping\Resource(method: 'POST', path: "/books", handler: 'YourCreateClass', middlewares: ['ProtectWriteMiddleware']),
]
class Book {
    #[\Rocket\Mapping\Id]
    #[
        \Rocket\Mapping\SubResource(method: 'GET'),
        \Rocket\Mapping\SubResource(method: 'PUT', middlewares: ['ProtectWriteMiddleware']),        
        \Rocket\Mapping\SubResource(method: 'DELETE', middlewares: ['ProtectWriteMiddleware']),
    ]
    public int $id;
    public string $name;
    public string $isbn;
}
```

With this definition you can build this routing table:

| Method | Url path | Handler |
| --- | --- | --- |
| `*` | `/authors` | `null` |
| `*` | `/authors/{id}` | `null` |
| `*` | `/authors/{id}/books` | `null` |
| `GET` | `/books` | `null` |
| `POST` | `/books` | `null` |
| `GET` | `/books/{id}` | `null` |
| `PUT` | `/books/{id}` | `null` |
| `DELETE` | `/books/{id}` | `null` |

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
