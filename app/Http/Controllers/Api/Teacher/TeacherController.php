<?php

namespace App\Http\Controllers\Api\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\StoreTeacherRequest;
use App\Http\Requests\Teacher\UpdateTeacherRequest;
use App\Models\Teacher;
use App\Services\MicroAuth\MicroAuthService;
use Exception;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    private $model;

    public function __construct(
        private Teacher $teacher,
        private MicroAuthService $microAuthService
    ) {
        $this->model = $teacher;
    }

    public function show(Request $request)
    {
        $teacher = $this->model->where('uuid', $request->user['id'])->first();

        if(!$teacher) return response()->json(['status' => 'failed', 'message' => 'Professor não encontrado'], 404);

        return response()->json(['data' => $teacher->makeHidden(['created_at', 'updated_at'])]);
    }


    public function update(UpdateTeacherRequest $request)
    {
        $teacher = $this->model->where('uuid', $request->user['id'])->first();

        if(!$teacher) return response()->json(['status' => 'failed', 'message' => 'Professor não encontrado'], 404);

        $dataForm = $request->except('user');
        $dataForm['uuid'] = $request->user['id'];

        try {
            $teacher->update($dataForm);

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

    public function delete(Request $request)
    {
        $teacher = $this->model->where('uuid', $request->user['id'])->first();

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
