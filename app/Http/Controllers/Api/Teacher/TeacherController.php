<?php

namespace App\Http\Controllers\Api\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\StoreTeacherRequest;
use App\Models\Teacher;
use Exception;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    private $model;

    public function __construct(Teacher $teacher)
    {
        $this->model = $teacher;
    }

    public function show(string $uuid)
    {
        $teacher = $this->model->where('uuid', $uuid)->first();

        if(!$teacher) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Registro não encontrado'
            ], 404);
        }

        return response()->json($teacher->makeHidden(['created_at', 'updated_at']));
    }

    public function store(StoreTeacherRequest $request)
    {
        try {
            $this->model->create($request->all());

            return response()->json(['status' => 'success', 'message' => 'Professor cadastrado com sucesso.'], 201);
        } catch(Exception $e) {
            return response()->json(['status' => 'failed', 'message' => 'Não foi possível cadastrar o professor.'], 500);
        }
    }
}
