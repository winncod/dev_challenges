<?php

declare(strict_types=1);

namespace App\Controller;

use App\Controller\BaseController;
use App\Exception\IssueException;
use App\Exception\Task;
use App\Service\IssueService;
use App\Service\UserService;


use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

final class IssueController extends BaseController
{
    protected function getIssueService(): IssueService
    {
        return $this->container->get('issue_service');
    }

    protected function getUserService(): UserService
    {
        return $this->container->get('user_service');
    }

    public function get(Request $request, Response $response): Response
    {
        $issue = $this->getRoute($request)->getArgument('issue');
        $data = $this->getIssueService()->get($issue);
        return $this->jsonResponse($response, $data);
    }

    public function join(Request $request, Response $response): Response
    {
        $issue = $this->getRoute($request)->getArgument('issue');
        $user = $this->data($request,'name');
        $this->getIssueService()->join($issue,$user);
        $token = $this->getUserService()->join($user); 
        return $this->jsonResponse($response, [
            'token' => $token
        ]);
    }

    public function vote(Request $request, Response $response): Response
    {
        $issue = $this->getRoute($request)->getArgument('issue');
        $auth = $this->auth($request);
        $data = null;
        if($auth!= null)
        {
            $data = $this->getIssueService()->vote($issue,$auth->username,(int)$this->data($request,'value'));
        }
        
        return $this->jsonResponse($response, $data);
    }
}
