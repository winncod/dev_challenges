<?php

declare(strict_types=1);

namespace App\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

final class DefaultController extends BaseController
{
    private const API_VERSION = '1.0';

    public function getHelp(Request $request, Response $response): Response
    {
        $app = $this->container->get('settings')['app'];
        
        $url = $app['domain'];
        $endpoints = [
            'issue' => $url . '/issue',
            'status' => $url . '/status',
            'this help' => $url . '',
        ];
        $message = [
            'endpoints' => $endpoints,
            'version' => self::API_VERSION,
            'timestamp' => time(),
        ];

        return $this->jsonResponse($response, $message);
    }

    public function getStatus(Request $request, Response $response): Response
    {
        $status = [
            'Redis' => $this->checkRedisConnection(),
            'version' => self::API_VERSION,
            'timestamp' => time(),
        ];

        return $this->jsonResponse($response, $status);
    }

    private function checkRedisConnection(): string
    {
        $redis = 'Disabled';
        if (self::isRedisEnabled() === true) {
            $redisService = $this->container->get('redis_service');
            $key = $redisService->generateKey('test:status');
            $redisService->set($key, new \stdClass());
            $redis = 'OK';
        }

        return $redis;
    }
}
