<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\IssueException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Container\ContainerInterface;
use Slim\Routing\RouteContext;

abstract class BaseController
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param array|object|null $message
     */
    protected function jsonResponse(Response $response,$message,int $code = 200): Response {
        $response = $response->withStatus($code);
        $response->getBody()->write(json_encode($message));
        return $response->withHeader('Content-Type', 'application/json');
        
    }

    protected static function isRedisEnabled(): bool
    {
        return filter_var($_SERVER['REDIS_ENABLED'], FILTER_VALIDATE_BOOLEAN);
    }

    protected function data($request,$param)
    {
        $contentType = $request->getHeaderLine('Content-Type');
        if (strstr($contentType, 'application/json')) 
        {
            $data = json_decode(file_get_contents('php://input'), true);
        }
        else
        {
            $data = (array)$request->getParsedBody();
        }
        
        if(array_key_exists($param,$data)) return $data[$param];
        throw new IssueException('Invalid Data',400);
    }

    protected function auth($request)
    {
        return $request->getAttribute('auth');
    }

    protected function getRoute($request): object
    {
        $routeContext = RouteContext::fromRequest($request);
        return $routeContext->getRoute();
    }
}
