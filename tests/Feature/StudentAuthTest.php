<?php

namespace Tests\Feature;

use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_register_and_login_and_access_protected_route(): void
    {
        $register = $this->postJson('/api/students/register', [
            'name' => 'Alice',
            'email' => 'alice@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ])->assertCreated();

        $this->assertArrayHasKey('token', $register->json());

        $login = $this->postJson('/api/students/login', [
            'email' => 'alice@example.com',
            'password' => 'secret123',
        ])->assertOk();

        $token = $login->json('token');

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/students/me')
            ->assertOk()
            ->assertJsonPath('student.email', 'alice@example.com');

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/students/logout')
            ->assertOk();
    }
}
