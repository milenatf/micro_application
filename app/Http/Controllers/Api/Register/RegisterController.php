<?php

namespace App\Http\Controllers\Api\Register;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Services\MicroAuth\MicroAuthService;
use App\Services\Student\StudentService;
use App\Services\Teacher\TeacherService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    public function __construct(
        private MicroAuthService $microAuthService,
        private Teacher $teacherModel,
        private StudentService $studentService,
        private TeacherService $teacherService
    ) { }

    public function store(Request $request): JsonResponse
    {
        try {

            $response = $this->microAuthService->register($request->all());

        } catch(Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Erro ao processar o registro: ' . $e->getMessage(),
            ], 500);
        }

        if($response->getStatusCode() === 201) {

            $result = json_decode($response->getContent());
            // return $result;

            $user = $this->teacherService->getByUuid('uuid', $result->id);

            if(!$user) {
                try {

                    $this->teacherService->register($result->id);

                    return response()->json(['data' => $result], 200);

                } catch(Exception $e) {
                    return response([
                        'status' => 'failed',
                        'message' => "Não foi possível adicionar o registro.",
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }
    }
}
