<?php

namespace Application;

use Framework\Http\{Path, Response};

function middleware(string $argument): string
{
    return 'Middleware: ' . $argument;
}

class Controller extends Response
{
    public function __invoke(): array
    {
        $this->charset('ASCII');

        return $this->response($this->url_path('style.css'));
    }

    public function media(Path $path): array|string
    {
        return $this->render_media($path->name);
    }

    public function archive(Path $path, callable $middleware): array
    {
        $url = $this->url_for('archive', year: '2025', month: '05', day: '25');
//        $url = var_export($this->url_for('archive', year: '2025', month: '05', err: '25'), true);

        $body = 'Path year: ' . $path->year;

        if (isset($path->month))
            $body .= ' month: ' . $path->month;

        if (isset($path->day))
            $body .= ' day: ' . $path->day;

        return $this->response($body . ' Url ' . $url . ' ' . $middleware('argument'));
    }
}