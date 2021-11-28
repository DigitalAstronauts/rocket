<?php
declare(strict_types=1);

namespace Rocket\Tests\Fixture\Resource;

use Faker\Factory;

class BookSeries
{
    private int $id;
    private string $name;
    private \DateTimeInterface $createdAt;
    private Author $author;
    private iterable $books;

    public function __construct()
    {
        $faker = Factory::create('cs_CZ');
        $this->id = $faker->numberBetween(1);
        $this->name = $faker->name;
        $this->createdAt = new \DateTime();
        $this->author = new Author();
        $books = [];
        foreach (range(3, mt_rand(3, 7)) as $i) {
            $books[] = new Book($this->author);
        }
        $this->books = new \ArrayIterator($books);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getBooks(): iterable
    {
        return $this->books;
    }

    public function setBooks(iterable $books): void
    {
        $this->books = $books;
    }
}