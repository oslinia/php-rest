<?php

namespace Framework\Foundation;

class Map extends Routing\Mapper
{
    public static string $path;

    public static function url(array $args): null|string
    {
        $name = array_shift($args);

        if (isset(parent::$urls[$name])) {
            $link = parent::$urls[$name];

            $size = count($args);

            if (isset($link[$size])) {
                [$path, $pattern] = $link[$size];

                foreach ($args as $mask => $value)
                    $path = str_replace('{' . $mask . '}', $value, $path);

                if (preg_match($pattern, $path, $matches))
                    return $matches[0];
            }
        }

        return null;
    }
}