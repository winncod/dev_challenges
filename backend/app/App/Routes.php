<?php

declare(strict_types=1);

use App\Controller\Note;
use App\Controller\Task;
use App\Controller\User;
use App\Middleware\Auth;

/** @var \Slim\App $app */

$app->get('/', 'App\Controller\DefaultController:getHelp');
$app->get('/status', 'App\Controller\DefaultController:getStatus');

$app->post('/issue/{issue}/join', 'App\Controller\IssueController:join');
$app->get('/issue/{issue}', 'App\Controller\IssueController:get');
$app->post('/issue/{issue}/vote', 'App\Controller\IssueController:vote')->add(new Auth());
