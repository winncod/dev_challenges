<?php

declare(strict_types=1);

use App\Service\RedisService;
use App\Service\IssueService;
use App\Service\UserService;


$container->set('settings', function () use($settings) {
    return $settings['settings'];
});

$container->set('redis_service', function ($container) {
    $redis = $container->get('settings')['redis'];

    return new RedisService(new \Predis\Client($redis['url']));
});

$container->set('user_service',  function ($container) {
    $redis = $container->get('redis_service');
    return new UserService($redis);
});

$container->set('issue_service',  function ($container) {
    $redis = $container->get('redis_service');
    return new IssueService($redis);
});

$container->set('notFoundHandler',  function () {
    return static function ($request, $response): void {
        throw new \Exception('Route Not Found.', 404);
    };
});
