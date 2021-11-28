<?php
declare(strict_types=1);

namespace Rocket\Tests\Fixture;

use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerBuilder;

class Serializer
{
    private static $instance;
    private $serializer;
    private $jsonContext;
    private function __construct()
    {
        $this->serializer = SerializerBuilder::create()->build();
        $this->jsonContext = SerializationContext::create()->enableMaxDepthChecks();
    }

    public static function serialize($object): string
    {
        return self::instance()->serializeJson($object);
    }

    private function serializeJson($object): string
    {
        return $this->serializer->serialize(
            $object,
            'json',
            $this->jsonContext
        );
    }

    public static function instance(): static
    {
        return self::$instance ?? self::$instance = new self();
    }
}