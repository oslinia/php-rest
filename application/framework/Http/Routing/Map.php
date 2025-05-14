<?php

namespace Framework\Http\Routing;

use Framework\Foundation\Routing\{Controller, Rule};

class Map
{
    public function rule(string $path, string $name): Rule
    {
        return new Rule($path, $name);
    }

    public function controller(string $name, string $class, null|string $method = null): Controller
    {
        return new Controller($name, $class, $method);
    }
}