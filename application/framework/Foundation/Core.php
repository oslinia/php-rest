<?php

namespace Framework\Foundation;

class Core extends Routing\Mapper
{
    public static null|string $encoding = null;
    public static string $resource;
    public static string $src;
    public static string $url;
    public static array $urls;

    public static function flag(): void
    {
        parent::$flag = true;
    }
}