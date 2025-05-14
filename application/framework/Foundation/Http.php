<?php

namespace Framework\Foundation;

class Http extends Routing\Mapper
{
    public static bool $flag = false;
    public static string $lang;
    public static object $dir;

    private function status(int $code): void
    {
        if (ob_get_level())
            ob_end_clean();

        header($_SERVER["SERVER_PROTOCOL"] . [
                200 => ' 200 OK',
                301 => ' 301 Moved Permanently',
                302 => ' 302 Moved Temporarily',
                307 => ' 307 Temporary Redirect',
                308 => ' 308 Permanent Redirect',
                404 => ' 404 Not Found',
                500 => ' 500 Internal Server Error',
            ][$code]);
    }

    private function content(int $length, string $mimetype, null|string $encoding): void
    {
        if (str_starts_with($mimetype, 'text/'))
            $mimetype .= '; charset=' . ($encoding ?? 'UTF-8');

        header('content-length: ' . $length);
        header('content-type: ' . $mimetype);
    }

    private function media(string $filename, null|string $encoding): void
    {
        $this->status(200);

        $this->content(filesize($filename), require 'mime.php', $encoding);

        if ($f = fopen($filename, 'rb')) {
            while (!feof($f))
                echo fread($f, 1024);

            fclose($f);
        }
    }

    private function document(
        string      $body,
        int|null    $code = null,
        null|string $mimetype = null,
        null|string $encoding = null): void
    {
        $this->status($code ?? 200);

        $this->content(strlen($body), $mimetype ?? 'text/plain', $encoding);

        echo $body;
    }

    public function __construct(string $routing)
    {
        $this->routing = $routing . DIRECTORY_SEPARATOR;

        parent::$flag || parent::caching($routing);

        $response = parent::response(explode('?', $_SERVER['REQUEST_URI'], 2)[0]);

        if (is_string($response))
            $this->document($response);

        elseif (is_array($response) and is_string($response[0]))
            self::$flag ? $this->media(...$response) : $this->document(...$response);

        else
            $this->document('Invalid data for response', 500, encoding: 'ASCII');
    }

    private static function config(object $config): void
    {
        self::$lang = $config->lang;

        Map::$path = $config->path;
    }

    public static function request(string $dirname): void
    {
        $resource = $dirname . DIRECTORY_SEPARATOR . 'resource' . DIRECTORY_SEPARATOR;
        $src = $dirname . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR;

        self::config(require $resource . 'config.php');

        self::$dir = (object)[
            'media' => $resource . 'media' . DIRECTORY_SEPARATOR,
            'template' => $src . 'template' . DIRECTORY_SEPARATOR,
        ];

        parent::$flag = is_dir($routing = $resource . 'routing');
        parent::$flag || parent::$tmp = array();

        require $src . 'app' . DIRECTORY_SEPARATOR . 'routing.php';

        new static($routing);
    }
}