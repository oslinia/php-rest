<?php

namespace Framework\Http;

use Framework\Foundation\Core;

function url_path(string $name): string
{
    return Core::$url . $name;
}

function url_for(string ...$args): string
{
    return Core::url($args) ?? '';
}

class Response
{
    public function url_path(string $name): string
    {
        return Core::$url . $name;
    }

    public function url_for(string ...$args): null|string
    {
        return Core::url($args);
    }

    public function charset(string $encoding): void
    {
        Core::$encoding = $encoding;
    }

    public function response(string $body, int|null $code = null, null|string $mimetype = null): array
    {
        return [$body, $code, $mimetype];
    }

    public function render_media(string $name): array|string
    {
        return Core::media($name) ?? ['File not found', 404, null, 'ASCII'];
    }

    public function context(mixed ...$args): void
    {
        Core::$context = $args;
    }

    public function render_template(string $name, int|null $code = null, string $mimetype = 'text/html'): array
    {
        return Core::template($name, $code, $mimetype) ?? ['Template not found', 500, null, 'ASCII'];
    }
}