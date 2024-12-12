<?php

namespace App\Http\Controllers\Api\Register;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Teacher;
use App\Services\MicroAuth\MicroAuthService;
use Exception;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    public function __construct(
        private MicroAuthService $microAuthService,
        private Student $studentModel,
        private Teacher $teacherModel
    ) { }

    public function store(Request $request)
    {
        $response = $this->microAuthService->register($request->all());

        if(!data_get($response->json(), 'error')) {

            $model = (bool) $response->json()['user']['is_teacher'] == false ? $this->studentModel : $this->teacherModel;
            $user = $model->where('uuid', $response->json()['user']['id'])->first();

            if(!$user) {
                try {
                    $model->create(['uuid' => $response->json()['user']['id']]);

                } catch(Exception $e) {
                    $userType = class_basename($model) === 'Teacher' ? 'professor' : 'aluno';

                    return response([
                        'status' => 'failed',
                        'message' => "Não foi possível adicionar o {$userType}",
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }

        return $response;
    }
}
