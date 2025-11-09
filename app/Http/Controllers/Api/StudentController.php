<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class StudentController extends Controller
{
    // auth student
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $student = Student::where('email', $credentials['email'])->first();
        if (! $student || ! \Hash::check($credentials['password'], $student->password)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials'],
            ]);
        }
        $token = $student->createToken('student-token')->plainTextToken;

        return response()->json([
            'student' => $student,
            'token' => $token,
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:students,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $student = Student::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        $token = $student->createToken('student-token')->plainTextToken;

        return response()->json([
            'student' => $student,
            'token' => $token,
        ], 201);
    }

    // logout student
    public function logout(Request $request)
    {
        /** @var Student $student */
        $student = $request->user();
        if ($student && $student->currentAccessToken()) {
            $student->currentAccessToken()->delete();
        }
        return response()->json(['message' => 'Logged out successfully']);
    }

    // forgot password
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:students,email',
        ]);

        $status = Password::broker('students')->sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json(['message' => __($status)]);
        }
        return response()->json(['message' => __($status)], 422);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'email' => 'required|email|exists:students,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $status = Password::broker('students')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (Student $student, $password) {
                $student->forceFill([
                    'password' => bcrypt($password),
                ])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json(['message' => __($status)]);
        }

        return response()->json(['message' => __($status)], 422);
    }

    public function me(Request $request)
    {
        return response()->json(['student' => $request->user()]);
    }
}
