<?php

use Framework\Foundation\Server;

/**
 * @var string $dirname
 */
require __DIR__ . '/../application/autoload.php';

Server::request($dirname);