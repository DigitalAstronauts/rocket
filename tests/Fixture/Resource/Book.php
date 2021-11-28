<?php
declare(strict_types=1);

namespace Rocket\Tests\Fixture\Resource;

use Faker\Factory;
use Rocket\Mapping as API;
use Rocket\Tests\Fixture\Middleware\EchoMiddleware;

#[API\Resource(
    path: "/book",
    middlewares: [
        EchoMiddleware::class
    ]
)]
class Book
{
    #[API\Id]
    public int $id;
    public string $name;
    public string $isbn;
    #[API\SubResource(path: "author")]
    public Author $author;

    public function __construct(Author $author)
    {
        $faker = Factory::create('cs_CZ');
        $this->id = $faker->numberBetween();
        $this->name = $faker->sentence();
        $this->isbn = $faker->isbn13();
        $this->author = $author;
    }
}