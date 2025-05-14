<?php

use Framework\Http\Routing\Map;

use Application\Controller;
use function Application\middleware;

$map = new Map;

$map->rule('/', 'index');
$map->controller('index', Controller::class);

$map->rule('/media/{name}', 'media')
    ->where(name: '[a-z]+\\.[a-z]+');
$map->controller('media', Controller::class, 'media');

$map->rule('/page/{name}', 'page')
    ->where(name: '[a-z]+\\.[a-z]+');
$map->controller('page', Controller::class, 'page');

$map->rule('/archive/{year}', 'archive')
    ->where(year: '[0-9]{4}');
$map->rule('/archive/{year}/{month}', 'archive')
    ->where(year: '[0-9]{4}', month: '[0-9]{1,2}');
$map->rule('/archive/{year}/{month}/{day}', 'archive')
    ->where(year: '[0-9]{4}', month: '[0-9]{1,2}', day: '[0-9]{1,2}');
$map->controller('archive', Controller::class, 'archive')
    ->middleware(fn(string $argument) => middleware($argument));