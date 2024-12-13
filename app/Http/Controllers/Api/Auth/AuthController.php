<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Services\MicroAuth\MicroAuthService;
use App\Services\Student\StudentService;
use App\Services\Teacher\TeacherService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AuthController extends Controller
{
    public function __construct(
        private Teacher $teacherModel,
        private StudentService $studentService,
        private TeacherService $teacherService,
        private MicroAuthService $microAuthService
    ) { }

    public function me()
    {
        // dd(auth()->user()->id);
        $authUser = auth()->user();
        $teacher = $this->teacherModel->where('uuid', $authUser->id)->first();

        if(!$teacher) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Registro não encontrado'
            ], 404);
        }

        $me = [
            'uuid' => $authUser->id,
            'name' => $authUser->name,
            'email' => $authUser->email,
            'email_verified_at' => $authUser->email_verified_at,
            'expertise' => $teacher->expertise,
            'experiense' => $teacher->experiense,
        ];

        return response()->json($me);
    }

    public function login(Request $request)
    {
        try {
            $response = $this->microAuthService->login($request);
        } catch (Exception $e) {
            return response()->json($e->getMessage());
        }

        if($response->getStatusCode() !== 200) {
            return $response;
        }

        $result = json_decode($response->getContent())->data;

        if(!$this->teacherService->getByUuid($result->id)) {
            $newUser = $this->teacherService->store($result->id);

            if(!$newUser) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Não foi possível autenticar o usuário.'
                ], 403);
            }
        }

        return response()->json([
            'token' => $result->token
        ]);
    }

    public function logout(Request $request)
    {
        $bearerToken = $request->header('Authorization');

        $this->microAuthService->logout($bearerToken);

        // Remover o token do cache
        Cache::forget($bearerToken);

        return response()->json(['message' => 'Logout realizado com sucesso.']);
    }
}
