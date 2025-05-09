<?php

namespace Framework\Foundation;

class Server extends Routing\Mapper
{
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

    private function mimetype(string $filename): string
    {
        return match (pathinfo($filename, PATHINFO_EXTENSION)) {
            'css' => 'text/css',
            'htm', 'html' => 'text/html',
            'txt' => 'text/plain',
            'xml' => 'text/xml',
            'gif' => 'image/gif',
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'svg' => 'image/svg+xml',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'pdf' => 'application/pdf',
            'ttf', 'otf' => 'font/ttf',
            'woff' => 'font/woff',
            'woff2' => 'font/woff2',
            default => 'application/octet-stream'
        };
    }

    private function content(int $length, string $mimetype, null|string $encoding): void
    {
        if (str_starts_with($mimetype, 'text/'))
            $mimetype .= '; charset=' . ($encoding ?? 'UTF-8');

        header('content-length: ' . $length);
        header('content-type: ' . $mimetype);
    }

    private function media(string $filename): void
    {
        $this->status(200);

        $this->content(filesize($filename), $this->mimetype($filename), Core::$encoding);

        if ($f = fopen($filename, 'rb')) {
            while (!feof($f))
                echo fread($f, 4096);

            fclose($f);
        }
    }

    private function document(
        string      $body,
        int|null    $code = null,
        null|string $mimetype = null): void
    {
        $this->status($code ?? 200);

        $this->content(strlen($body), $mimetype ?? 'text/plain', Core::$encoding);

        echo $body;
    }

    public function __construct(string $routing)
    {
        parent::$flag = is_dir($routing);
        parent::$flag || parent::$tmp = array();

        require Core::$src . 'app' . DIRECTORY_SEPARATOR . 'routing.php';

        $this->routing = $routing . DIRECTORY_SEPARATOR;

        parent::$flag || parent::caching($routing);

        Core::$urls = require $this->routing . 'urls.php';

        $response = $this->response(explode('?', $_SERVER['REQUEST_URI'], 2)[0]);

        if (is_string($response))
            parent::$flag ? $this->media($response) : $this->document($response);

        elseif (is_array($response) and is_string($response[0]))
            $this->document(...$response);

        else
            throw new \ValueError('Body value in return array is not a string');
    }

    public static function request(string $dirname): void
    {
        Core::$resource = $dirname . DIRECTORY_SEPARATOR . 'resource' . DIRECTORY_SEPARATOR;
        Core::$src = $dirname . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR;

        new static(Core::$resource . 'routing');
    }
}