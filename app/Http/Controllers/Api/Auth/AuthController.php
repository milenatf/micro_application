<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Teacher;
use App\Services\MicroAuth\MicroAuthService;
use App\Services\Student\StudentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AuthController extends Controller
{
    public function __construct(
        private Teacher $teacherModel,
        private Student $studentModel,
        private StudentService $studentService,
        private MicroAuthService $microAuthService
    ) { }

    public function me(Request $request)
    {
        $model = $request->user['is_teacher'] === 0 ? $this->studentModel : $this->teacherModel;
        $user = $model->where('uuid', $request->user['id'])->first();

        if(!$user) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Registro não encontrado'
            ], 404);
        }

        $authUser = [
            'uuid' => $user->uuid,
            'name' => $request->user['name'],
            'email' => $request->user['email'],
            'is_teacher' => $request->user['is_teacher'],
            'email_verified_at' => $request->user['email_verified_at'],
            'expertise' => $user->expertise,
            'experiense' => $user->experiense,
        ];

        return response()->json($authUser);
    }

    public function login(Request $request)
    {
        $response = $this->microAuthService->login($request);

        if(!data_get($response->json(), 'user')) {
            return $response->json();
        }

        $role = $response->json()['user']['is_teacher'];
        $uuid = $response->json()['user']['id'];

        $service = $role === 0 ? $this->studentService : '$this->teacherModel';

        if(!$service->getByUuid($uuid)) {
            $newUser = $service->store($uuid);

            if(!$newUser) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Não foi possível autenticar o usuário.'
                ], 403);
            }
        }

        return response()->json([
            'token' => $response->json()['user']['token']
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
