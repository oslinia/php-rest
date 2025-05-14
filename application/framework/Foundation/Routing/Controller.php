<?php

namespace Framework\Foundation\Routing;

class Controller extends Mapper
{
    private string $name;

    public function __construct(string $name, string $class, null|string $method)
    {
        parent::$controller[$name] = [$class, $method ?? '__invoke', array()];

        $this->name = $name;
    }

    public function middleware(mixed ...$args): void
    {
        parent::$controller[$this->name][2] = $args;
    }
}