<?php

namespace Framework\Http;

use Framework\Foundation\Core;

class Response
{
    public function charset(string $encoding): void
    {
        Core::$encoding = $encoding;
    }

    public function render_media(string $name): array|string
    {
        $filepath = Core::$resource . 'media' . DIRECTORY_SEPARATOR . $name;

        if (is_file($filepath)) {
            Core::flag();

            return $filepath;
        }

        return ['File not found', 404, null, 'ASCII'];
    }

    public function url_path(string $name): string
    {
        return Core::$url . $name;
    }

    public function url_for(string ...$args): null|string
    {
        $name = array_shift($args);

        if (isset(Core::$urls[$name])) {
            $link = Core::$urls[$name];

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

    public function response(string $body, int|null $code = null, null|string $mimetype = null): array
    {
        return [$body, $code, $mimetype];
    }
}