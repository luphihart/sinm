<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Murid;
use App\Models\Kelas;
use App\Models\Jurusan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class MuridResetPasswordTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $jurusan;
    protected $kelas;

    protected function setUp(): void
    {
        parent::setUp();

        // Create admin user
        $this->admin = User::create([
            'name' => 'Admin User',
            'username' => 'admin_test',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Create dependency records
        $this->jurusan = Jurusan::create([
            'kode_jurusan' => 'RPL',
            'nama_jurusan' => 'Rekayasa Perangkat Lunak',
        ]);

        $this->kelas = Kelas::create([
            'jurusan_id' => $this->jurusan->id,
            'nama_kelas' => 'XII RPL 1',
            'tingkat' => 'XII',
        ]);
    }

    public function test_admin_can_reset_single_student_password_to_siswa123(): void
    {
        // Create student user & student record
        $studentUser = User::create([
            'name' => 'Student User',
            'username' => '22001',
            'password' => Hash::make('old_password'),
            'role' => 'murid',
        ]);

        $student = Murid::create([
            'user_id' => $studentUser->id,
            'nis' => '22001',
            'nama_lengkap' => 'Student User',
            'jenis_kelamin' => 'L',
            'kelas_id' => $this->kelas->id,
            'angkatan' => 2022,
            'status' => 'aktif',
        ]);

        // Post to single reset password route as admin
        $response = $this->actingAs($this->admin)
            ->post(route('admin.murid.reset-password', $student->id));

        $response->assertRedirect(route('admin.murid.index'));
        $response->assertSessionHas('success');

        // Check if password has been reset to default 'siswa123'
        $studentUser->refresh();
        $this->assertTrue(Hash::check('siswa123', $studentUser->password));
    }

    public function test_admin_can_bulk_reset_students_passwords_to_siswa123(): void
    {
        // Create student users & student records
        $students = [];
        $studentUsers = [];
        for ($i = 1; $i <= 3; $i++) {
            $user = User::create([
                'name' => "Student User $i",
                'username' => "2200$i",
                'password' => Hash::make('old_password'),
                'role' => 'murid',
            ]);

            $student = Murid::create([
                'user_id' => $user->id,
                'nis' => "2200$i",
                'nama_lengkap' => "Student User $i",
                'jenis_kelamin' => 'L',
                'kelas_id' => $this->kelas->id,
                'angkatan' => 2022,
                'status' => 'aktif',
            ]);

            $students[] = $student;
            $studentUsers[] = $user;
        }

        // Post to bulk reset route as admin
        $response = $this->actingAs($this->admin)
            ->post(route('admin.murid.bulk-reset-password'), [
                'murid_ids' => [
                    $students[0]->id,
                    $students[1]->id,
                    $students[2]->id,
                ]
            ]);

        $response->assertRedirect(route('admin.murid.index'));
        $response->assertSessionHas('success');

        // Check all passwords
        foreach ($studentUsers as $user) {
            $user->refresh();
            $this->assertTrue(Hash::check('siswa123', $user->password));
        }
    }

    public function test_bulk_reset_requires_at_least_one_student(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.murid.bulk-reset-password'), [
                'murid_ids' => []
            ]);

        $response->assertSessionHasErrors(['murid_ids']);
    }

    public function test_non_admin_cannot_reset_student_password(): void
    {
        $studentUser = User::create([
            'name' => 'Student User',
            'username' => '22001',
            'password' => Hash::make('old_password'),
            'role' => 'murid',
        ]);

        $student = Murid::create([
            'user_id' => $studentUser->id,
            'nis' => '22001',
            'nama_lengkap' => 'Student User',
            'jenis_kelamin' => 'L',
            'kelas_id' => $this->kelas->id,
            'angkatan' => 2022,
            'status' => 'aktif',
        ]);

        // Try single reset as student user
        $response = $this->actingAs($studentUser)
            ->post(route('admin.murid.reset-password', $student->id));

        $response->assertStatus(403);

        // Try bulk reset as student user
        $response = $this->actingAs($studentUser)
            ->post(route('admin.murid.bulk-reset-password'), [
                'murid_ids' => [$student->id]
            ]);

        $response->assertStatus(403);
    }
}
