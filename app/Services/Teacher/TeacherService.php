<?php

namespace App\Services\Teacher;

use App\Repositories\Teacher\TeacherRepository;

class TeacherService
{
    public function __construct(
        protected TeacherRepository $repository
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