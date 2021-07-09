<?php

declare(strict_types=1);

use App\Exception\Base;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

$errorMiddleware = $app->addErrorMiddleware(true, true, true);
$customErrorHandler = function (
    ServerRequestInterface $request,
    Throwable $exception,
    bool $displayErrorDetails,
    bool $logErrors,
    bool $logErrorDetails,
    ?LoggerInterface $logger = null
) use ($app) {
    
    $payload = ['error' => $exception->getMessage()];

    $response = $app->getResponseFactory()->createResponse();
    $response->getBody()->write(
        json_encode($payload, JSON_UNESCAPED_UNICODE)
    );
    
    if ($exception instanceof  Base) 
    {
        $response = $response->withStatus($exception->getCode());
    }
    else
    {
        $response = $response->withStatus(500);
    }
    

    return $response;
};
$errorMiddleware->setDefaultErrorHandler($customErrorHandler);
