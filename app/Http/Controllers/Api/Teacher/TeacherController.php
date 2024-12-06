<?php

namespace App\Http\Controllers\Api\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\StoreTeacherRequest;
use App\Models\Teacher;
use App\Services\MicroAuth\MicroAuthService;
use Exception;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    private $model;

    public function __construct(
        Teacher $teacher,
        private MicroAuthService $microAuthService
    ) {
        $this->model = $teacher;
    }

    public function me(Request $request)
    {
        $teacher = $this->model->where('uuid', $request->user['id'])->first()->makeHidden(['created_at', 'updated_at', 'id', 'uuid']);

        if(!$teacher) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Registro não encontrado'
            ], 404);
        }

        $authUser = collect($request->user)->merge($teacher);

        return response()->json($authUser);
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
