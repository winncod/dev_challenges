<?php

declare(strict_types=1);

use DI\Container;
use Slim\Factory\AppFactory;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

require __DIR__ . '/../../vendor/autoload.php';
$baseDir = __DIR__ . '/../../';

$dotenv = Dotenv\Dotenv::createImmutable($baseDir);
$envFile = $baseDir . '.env';
if (file_exists($envFile)) {
    $dotenv->load();
}
$dotenv->required(['REDIS_ENABLED']);

$settings = require __DIR__ . '/Settings.php';
//$app = new \Slim\App($settings);

//$app->add(new \CorsSlim\CorsSlim());

$container = new Container();
$app = AppFactory::createFromContainer($container);

require __DIR__ . '/Dependencies.php';
require __DIR__ . '/Routes.php';
require __DIR__ . '/Error.php';
require __DIR__ . '/Cors.php';