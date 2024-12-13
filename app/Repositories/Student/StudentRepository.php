<?php

namespace App\Repositories\Student;

use App\Models\Student;
use Exception;
use Illuminate\Http\JsonResponse;

class StudentRepository
{
    public function __construct(
        private Student $model
    ) {}


    public function getByUuid(string $uuid): null|JsonResponse
    {
        $student = $this->model->where('uuid', $uuid)->first();

        if(!$student) return null;

        return response()->json($student);
    }

    public function store(array $data): bool|JsonResponse
    {
        dd($data);
        try {
            $this->model->create($data);
            // return true;
        } catch(Exception $e) {
            // return false;
            return $e->getMessage();
        }
    }
}