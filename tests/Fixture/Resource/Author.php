<?php
declare(strict_types=1);

namespace Rocket\Tests\Fixture\Resource;

use Faker\Factory;
use Rocket\Mapping as API;
use Rocket\Tests\Fixture\Middleware\EchoMiddleware;

#[API\Resource(path: "/author", middlewares: [EchoMiddleware::class])]
class Author
{
    #[API\Id]
    #[
        API\SubResource(method: 'GET'),
        API\SubResource(method: 'POST', middlewares: [EchoMiddleware::class]),
        API\SubResource(method: 'PUT', middlewares: [EchoMiddleware::class]),
        API\SubResource(method: 'DELETE', middlewares: [EchoMiddleware::class]),
    ]
    public int $id;
    public string $name;
    /** @var Book[] */
    #[API\SubResource(path: "books", resourceClass: Book::class)]
    public iterable $books;

    public function __construct()
    {
        $faker = Factory::create('cs_CZ');
        $this->id = $faker->numberBetween();
        $this->name = $faker->name();
        $this->books = [];
        foreach (range(1, mt_rand(3, 10)) as $i) {
            $this->books[] = new Book($this);
        }
    }
}