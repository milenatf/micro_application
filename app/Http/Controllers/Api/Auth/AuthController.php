<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Teacher;
use App\Services\MicroAuth\MicroAuthService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    public function __construct(
        private Teacher $teacherModel,
        private Student $studentModel,


        private MicroAuthService $microAuthService
    ) { }

    public function me(Request $request)
    {
        // dd($request->is_teacher);
        // $model = $request->is_teacher === 1 ? $this->teacherModel : $this->studentModel;

        $user = null;

        if($request->is_teacher === 0) {

            $user = $this->studentModel->where('uuid', $request->user['id'])->first()->makeHidden(['createad_at', 'updated_at']);
        }

        if($request->is_teacher === 1) {
            dd("aasdfasdfa");
            $user = $this->teacherModel->where('uuid', $request->user['id'])->first();
        }

        dd($user);


        if(!$user) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Registro não encontrado'
            ], 404);
        }

        $authUser = collect($request->user)->merge($user);

        return response()->json($authUser);
    }

    public function login(Request $request)
    {
        $response = $this->microAuthService->login($request);

        return response()->json([
            'token' => $response->json()['user']['token']
        ]);
    }
}
