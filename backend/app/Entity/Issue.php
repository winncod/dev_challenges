<?php

declare(strict_types=1);

namespace App\Entity;

final class Issue
{
    private string $code;

    private string $status;

    private array $members;

    private float $avg;

    public function toJson(): object
    {
        return json_decode((string) json_encode(get_object_vars($this)), false);
    }

    public function toArray(): array
    {
        $data = [
            'code' => $this->code,
            'status' => $this->status,
            'members' => $this->members ?? [],
            'avg' => $this->avg ?? 0
        ];
        if($this->status == 'voting')
        {
            $hiddenCopy = [];
            foreach ($data['members'] as $key => $value) 
            {
                $hiddenCopy[] = [
                    'name'=> $value['name'],
                    'status' => $value['status']
                ];
            }
            $data['members'] = $hiddenCopy;
            $data['avg'] = 0;
        }
        return $data; 
    }


    public function getCode(): string
    {
        return $this->code;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getMembers(): array
    {
        return $this->members ?? [];
    }

    public function getAvg():float
    {
        return $this->avg;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function setMembers(array $members): self
    {
        $this->members = $members;

        return $this;
    }

    public function setAvg(float $avg): self
    {
        $this->avg = $avg;

        return $this;
    }
}
