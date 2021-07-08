<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Exception\AuthException;
use App\Service\UserService;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;
use Firebase\JWT\JWT;

final class Auth 
{
    public function __invoke(Request $request,RequestHandler  $handler) 
    {
        $response = $handler->handle($request);
        $jwtHeader = $request->getHeaderLine('Authorization');

        if (! $jwtHeader) {
            throw new AuthException('JWT Token required.', 400);
        }
        $jwt = explode('Bearer ', $jwtHeader);
        if (! isset($jwt[1])) {
            throw new AuthException('JWT Token invalid.', 400);
        }
        $decoded = $this->checkToken($jwt[1]);
        
        $request = $request->withAttribute('auth', $decoded);
        
        return $handler->handle($request);
    }

    protected function checkToken(string $token): object
    {
        try {
            return JWT::decode($token, UserService::JWT_KEY, ['HS256']);
        } catch (\UnexpectedValueException $exception) {
            throw new AuthException('Forbidden: you are not authorized.', 403);
        }
    }
}
