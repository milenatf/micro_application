<?php

namespace App\Http\Controllers\Api\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\StoreTeacherRequest;
use App\Http\Requests\Teacher\UpdateTeacherRequest;
use App\Models\Student;
use App\Models\Teacher;
use App\Services\MicroAuth\MicroAuthService;
use App\Services\Teacher\TeacherService;
use Exception;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    public function __construct(
        private Teacher $model,
        private TeacherService $service,
        private Student $student,
        private MicroAuthService $microAuthService
    ) { }

    public function store(StoreTeacherRequest $request)
    {
        $authUser = auth()->user();
        $data = $request->all();
        $data['uuid'] = $authUser->id;

        if(!$this->service->store($data)) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Não foi possível inserir o registro.'
            ], 500);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Registro inserido com sucesso.'
        ], 201);
    }

    public function show(string $user_id = null)
    {
        if( $user_id === null) {
            $user_id = auth()->user()->id;
        }

        $teacher = $this->model->where('uuid', $user_id)->first();

        if(!$teacher) return response()->json(['status' => 'failed', 'message' => 'Professor não encontrado'], 404);

        return response()->json(['data' => $teacher->makeHidden(['created_at', 'updated_at'])]);
    }


    public function update(UpdateTeacherRequest $request)
    {
        $user_id = auth()->user()->id;

        $teacher = $this->model->where('uuid', $user_id)->first();

        if(!$teacher) return response()->json(['status' => 'failed', 'message' => 'Professor não encontrado'], 404);

        try {
            $teacher->update($request->all());

            return response()->json([
                'status' => 'success',
                'Message' => 'Atualização realizada com sucesso.'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Não foi possível atualizar os dados',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function delete()
    {
        $user_id = auth()->user()->id;
        $teacher = $this->model->where('uuid', $user_id)->first();

        if(!$teacher) return response()->json(['status' => 'failed', 'message' => 'Professor não encontrado'], 404);

        try {
            $teacher->delete();

            return response()->json([
                'status' => 'success',
                'Message' => 'Professor excluído com sucesso.'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Não foi possível excluir o professor',
                'error' => $e->getMessage()
            ]);
        }
    }
}
