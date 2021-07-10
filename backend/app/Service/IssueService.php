<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Issue;
use App\Entity\User;
use App\Exception\IssueException;
use App\Service\BaseService;
use App\Service\RedisService;
use Respect\Validation\Validator as v;

class IssueService //Can not be final, to can mock it
{
    private const REDIS_KEY = 'issue:%s';

    protected RedisService $redisService;

    public function __construct(RedisService $redisService) 
    {
        $this->redisService = $redisService;
    }

    protected function loadJson($json):Issue
    {
        $issue = new Issue();
        $issueData = json_decode($json, true);
        
        if(array_key_exists('code',$issueData)) $issue->setCode($issueData['code']);
        if(array_key_exists('status',$issueData)) $issue->setStatus($issueData['status']);
        if(array_key_exists('members',$issueData)) $issue->setMembers($issueData['members']);
        if(array_key_exists('avg',$issueData)) $issue->setAvg($issueData['avg']);
        return $issue;
    }

    protected function exists_key_value($array, $key, $val)
    {
        foreach ($array as $item)
        {
            if (is_array($item) && $this->exists_key_value($item, $key, $val)) return true;

            if (isset($item[$key]) && $item[$key] == $val) return true;
        }
        return false;
    }

    protected function getIssue($issue): ?Issue
    {
        $redisKey = sprintf(self::REDIS_KEY, $issue);
        $key = $this->redisService->generateKey($redisKey);
        if ($this->redisService->exists($key)) {
            $data = $this->redisService->get($key);
            $issueData = $this->loadJson((string) json_encode($data));
            return $issueData;
        } 
        return null;
    }

    protected function setIssue(Issue $issueData)
    {
        $redisKey = sprintf(self::REDIS_KEY, $issueData->getCode());
        $key = $this->redisService->generateKey($redisKey);
        $this->redisService->setex($key, $issueData->toJson());
    }

    public function get(string $issue): array
    {
        $issueData = $this->getIssue($issue);
        if($issueData == null) throw new IssueException('Not found',404);   
        return $issueData->toArray();
    }
    
    public function join(string $issue,string $user): object
    {
        $issueData = $this->getIssue($issue); 
        if($issueData == null) $issueData = new Issue();
        
        $issueData->setCode($issue);
        $members = $issueData->getMembers();
        if(!$this->exists_key_value($members,'name',$user))
        {
            $members[] = [
                'name' => $user,
                'status' => 'waiting',
                'value' => 0
            ];
            $issueData->setStatus('voting');
            $issueData->setMembers($members);
        }
        $this->setIssue($issueData);
        return $issueData;
    }

    public function vote(string $issue,string $username,int $vote)
    {
        $issueData = $this->getIssue($issue);
        if($issueData == null) 
            throw new IssueException('Not found',404);            
        if($issueData->getStatus() !== 'voting') 
            throw new IssueException('The status of issue is not allowed to vote',400);            
        if(!$this->exists_key_value($issueData->getMembers(),'name',$username) ) 
            throw new IssueException('To vote the user must be joined to the issue',400);            
        $members = $issueData->getMembers();
        
        $sum = 0;
        $totalVotes = 0; 
        $totalPassed = 0; 
        
        foreach ($members as $key => $value) 
        {
            
            if($value['name'] == $username) 
            {
                if($value['status'] != 'waiting')
                {
                    throw new IssueException('You already voted on this issue',400);      
                }
                $value['status'] = $vote !== 0 ? 'voted':'passed';
                $value['value'] = $vote;
                $members[$key] = $value;
            }
            if($value['status'] == 'voted')
            {
                $sum += (float)$value['value'];
                $totalVotes +=1;
            }
            if($value['status'] == 'passed')
            {
                $totalPassed +=1;
            }
        }
        $issueData->setMembers($members);
        $issueData->setAvg($totalVotes > 0 ? ($sum/$totalVotes): 0);
        if(($totalVotes + $totalPassed) == count($members) )
        {
            $issueData->setStatus('reveal');
        }

        $this->setIssue($issueData);
        return $issueData->toArray();
    }
    
}
