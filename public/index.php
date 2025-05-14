<?php

use Framework\Foundation\Http;

/**
 * @var string $dirname
 */
require __DIR__ . '/../application/autoload.php';

Http::request($dirname);