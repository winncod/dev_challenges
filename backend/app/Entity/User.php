<?php

declare(strict_types=1);

namespace App\Entity;

final class User
{
    private string $username;

    private string $token;

    public function toJson(): object
    {
        return json_decode((string) json_encode(get_object_vars($this)), false);
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }
}
