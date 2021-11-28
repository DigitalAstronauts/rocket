<?php
declare(strict_types=1);

namespace Rocket\Tests;

use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\ServerRequestFactory;
use League\Route\Router;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Rocket\MappingFactory;

class MappingFactoryTest extends TestCase
{
    /**
     * @dataProvider provideRequestInputs
     */
    public function testRuntime(string $uri, string $method): void
    {
        $router = $this->createRouter();
        $request = ServerRequestFactory::fromGlobals([
            'REQUEST_URI' => $uri,
            'REQUEST_METHOD' => $method,
        ]);
        $response = $router->dispatch($request);
        Assert::assertInstanceOf(JsonResponse::class, $response);
        $json = json_decode($response->getBody()->getContents(), true);
        Assert::assertArrayHasKey('ok', $json);
        Assert::assertEquals($json['ok'], true);
        Assert::assertEquals(200, $response->getStatusCode());
    }

    public function provideRequestInputs(): array
    {
        return [
            ['/author', 'GET'],
            ['/author/{id}', 'GET'],
            ['/author', 'POST'],
            ['/author', 'PUT'],
            ['/author', 'DELETE'],
            ['/author/1/books', 'GET'],
            ['/book', 'GET'],
            ['/book/1/author', 'GET'],
        ];
    }

    private function createRouter(): Router
    {
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
                    rtrim(sprintf('%s/{%s}/%s', $subResource->parentPath, $collection->getId()->name, $subResource->path), '/'),
                    $handler
                );
                foreach ($subResource->middlewares as $middleware) {
                    $route->middleware(new $middleware());
                }
            }
        }
        return $router;
    }
}
