<?php

namespace Framework\Foundation\Routing;

class Controller extends Mapper
{
    private string $name;

    public function __construct(string $name, string $class, null|string $method)
    {
        self::$controller[$name] = [$class, $method ?? '__invoke', array()];

        $this->name = $name;
    }

    public function middleware(mixed ...$args): void
    {
        self::$controller[$this->name][2] = $args;
    }
}