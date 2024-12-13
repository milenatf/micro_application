<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Services\Student\StudentService;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function __construct(
        private StudentService $service
    ) { }
    public function index()
    {
        $authUser = auth()->user();
        $students = Student::where('teacher_id', $authUser->id);

        if(!$students) return response()->json(['message' => 'Não há registros'], 404);

        return response()->json([
            'data' => $students
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $data['teacher_id'] = auth()->user()->id;

        $newStudent = $this->service->store($data);

        if(!$newStudent) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Não foi possível inserir o aluno.'
            ], 500);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Aluno inserido com sucesso.'
        ], 201);
    }
}
