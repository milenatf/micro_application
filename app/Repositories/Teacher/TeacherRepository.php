<?php

namespace App\Repositories\Teacher;

use App\Models\Teacher;
use Exception;
use Illuminate\Http\JsonResponse;

class TeacherRepository
{
    public function __construct(
        private Teacher $model
    ) {}


    public function getByUuid(string $uuid): null|JsonResponse
    {
        $teacher = $this->model->where('uuid', $uuid)->first();

        if(!$teacher) return null;

        return response()->json($teacher);
    }

    public function register(string $uuid): bool
    {
        try {

            $this->model->create(['uuid' => $uuid]);

            return true;

        } catch(Exception $e) {

            return false;

        }
    }

    public function store(array $data): bool
    {
        try {

            $this->model->create($data);
            return true;

        } catch(Exception $e) {

            return false;

        }
    }
}