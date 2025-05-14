<?php

namespace Framework\Http;

use Framework\Foundation\{Http, Map};

function url_path(string $name): string
{
    return Map::$path . $name;
}

function url_for(string ...$args): string
{
    return Map::url($args) ?? '';
}

function template_buffer(object $buffer): string
{
    extract($buffer->extract);

    ob_start();

    require $buffer->require;

    return ob_get_clean();
}

class Response
{
    private null|string $encoding = null;
    private array|null $context = null;
    private object $dir;

    public function __construct()
    {
        $this->dir =& Http::$dir;
    }

    public function url_path(string $name): string
    {
        return Map::$path . $name;
    }

    public function url_for(string ...$args): null|string
    {
        return Map::url($args);
    }

    public function charset(string $encoding): void
    {
        $this->encoding = $encoding;
    }

    public function response(
        string      $body,
        int|null    $code = null,
        null|string $mimetype = null,
        null|string $encoding = null): array
    {
        return [$body, $code, $mimetype, $encoding ?? $this->encoding];
    }

    public function render_media(string $name, null|string $encoding = null): array
    {
        $filename = $this->dir->media . $name;

        if (is_file($filename)) {
            Http::$flag = true;

            return [$filename, $encoding];
        }

        return ['File not found', 404, null, 'ASCII'];
    }

    public function context(mixed ...$args): void
    {
        $this->context = $args;
    }

    public function render_template(
        string      $name,
        int|null    $code = null,
        null|string $mimetype = null,
        null|string $encoding = null): array
    {
        $filename = $this->dir->template . $name;

        if (is_file($filename)) {
            $context = ['lang' => Http::$lang];

            return [template_buffer((object)[
                'extract' => is_null($this->context) ? $context : array_merge($context, $this->context),
                'require' => $filename,
            ]), $code, $mimetype ?? 'text/html', $encoding ?? $this->encoding];
        }

        return ['Template not found', 500, null, 'ASCII'];
    }
}