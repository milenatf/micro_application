<?php

namespace App\Services\Student;

use App\Repositories\Student\StudentRepository;

class StudentService
{
    public function __construct(
        protected StudentRepository $repository
    ) { }

    public function getByUuid(string $uuid)
    {
        return $this->repository->getByUuid($uuid);
    }

    public function store(string $uuid)
    {
        return $this->repository->store($uuid);
    }
}