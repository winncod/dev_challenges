<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use App\Service\BaseService;
use App\Service\RedisService;
use Respect\Validation\Validator as v;
use Firebase\JWT\JWT;

final class UserService
{
    private const REDIS_KEY = 'user:%s';
    public const JWT_KEY = 'htpc';

    protected RedisService $redisService;

    public function __construct(RedisService $redisService) 
    {
        $this->redisService = $redisService;
    }

    protected static function validateUserName(string $name): string
    {
        if (! v::alnum('ÁÉÍÓÚÑáéíóúñ.')->length(1, 100)->validate($name)) {
            throw new \App\Exception\User('Invalid name.', 400);
        }

        return $name;
    }

    protected function getUserFromCache(int $userId): object
    {
        $redisKey = sprintf(self::REDIS_KEY, $userId);
        $key = $this->redisService->generateKey($redisKey);
        if ($this->redisService->exists($key)) {
            $data = $this->redisService->get($key);
            $user = json_decode((string) json_encode($data), false);
        } else {
            $user = $this->getUserFromDb($userId)->toJson();
            $this->redisService->setex($key, $user);
        }

        return $user;
    }

    public function join(string $name)
    {
        $redisKey = sprintf(self::REDIS_KEY, $name);
        $key = $this->redisService->generateKey($redisKey);
        $user = new User();
        $user->setUsername($name);
        $jwt = JWT::encode([
            'username'=>$user->getUsername()
        ], self::JWT_KEY);
        $this->redisService->setex($key, $user);
        return $jwt;
    }

    protected function saveInCache(int $id, object $user): void
    {
        $redisKey = sprintf(self::REDIS_KEY, $id);
        $key = $this->redisService->generateKey($redisKey);
        $this->redisService->setex($key, $user);
    }

    protected function deleteFromCache(int $userId): void
    {
        $redisKey = sprintf(self::REDIS_KEY, $userId);
        $key = $this->redisService->generateKey($redisKey);
        $this->redisService->del([$key]);
    }
}
