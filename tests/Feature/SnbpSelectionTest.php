<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Murid;
use App\Models\Kelas;
use App\Models\Jurusan;
use App\Models\Semester;
use App\Models\MataPelajaran;
use App\Models\Nilai;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SnbpSelectionTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $jurusan;
    protected $kelas;
    protected $semesters = [];
    protected $mapel;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Create Admin
        $this->admin = User::create([
            'name' => 'Admin SNBP',
            'username' => 'admin_snbp',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // 2. Create Jurusan & Kelas
        $this->jurusan = Jurusan::create([
            'kode_jurusan' => 'RPL',
            'nama_jurusan' => 'Rekayasa Perangkat Lunak',
            'kuota_snbp' => 2, // Quota of 2 eligible students
        ]);

        $this->kelas = Kelas::create([
            'jurusan_id' => $this->jurusan->id,
            'nama_kelas' => 'XII RPL 1',
            'tingkat' => 'XII',
        ]);

        // 3. Create Semesters (1-6)
        for ($i = 1; $i <= 6; $i++) {
            $this->semesters[$i] = Semester::create([
                'semester_ke' => $i,
                'tahun_ajaran' => '2023/2024',
            ]);
        }

        // 4. Create Mata Pelajaran
        $this->mapel = MataPelajaran::create([
            'kode_mapel' => 'MAPEL01',
            'nama_mapel' => 'Matematika',
            'urutan' => 1,
        ]);
    }

    public function test_admin_can_update_snbp_settings(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.snbp.settings'), [
                'snbp_menu_status' => 'aktif',
                'snbp_deadline' => '2026-06-30T23:59',
            ]);

        $response->assertRedirect(route('admin.snbp.index'));
        $response->assertSessionHas('success');

        $this->assertEquals('aktif', Setting::get('snbp_menu_status'));
        $this->assertEquals('2026-06-30T23:59', Setting::get('snbp_deadline'));
    }

    public function test_admin_can_update_snbp_quota(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.snbp.quota'), [
                'jurusan_id' => $this->jurusan->id,
                'kuota_snbp' => 5,
            ]);

        $response->assertRedirect(route('admin.snbp.index', ['jurusan_id' => $this->jurusan->id]));
        $response->assertSessionHas('success');

        $this->jurusan->refresh();
        $this->assertEquals(5, $this->jurusan->kuota_snbp);
    }

    public function test_murid_can_register_for_snbp_before_deadline(): void
    {
        Setting::set('snbp_menu_status', 'aktif');
        Setting::set('snbp_deadline', now()->addDays(2)->toIso8601String());

        $studentUser = User::create([
            'name' => 'Student A',
            'username' => '22001',
            'password' => Hash::make('password'),
            'role' => 'murid',
        ]);

        $student = Murid::create([
            'user_id' => $studentUser->id,
            'nis' => '22001',
            'nama_lengkap' => 'Student A',
            'jenis_kelamin' => 'L',
            'kelas_id' => $this->kelas->id,
            'angkatan' => 2022,
            'status' => 'aktif',
        ]);

        $response = $this->actingAs($studentUser)
            ->post(route('murid.snbp.daftar'));

        $response->assertRedirect(route('murid.snbp.index'));
        $response->assertSessionHas('success');
        $this->assertTrue($student->snbpPendaftar()->exists());
    }

    public function test_murid_can_withdraw_from_snbp_before_deadline(): void
    {
        Setting::set('snbp_menu_status', 'aktif');
        Setting::set('snbp_deadline', now()->addDays(2)->toIso8601String());

        $studentUser = User::create([
            'name' => 'Student A',
            'username' => '22001',
            'password' => Hash::make('password'),
            'role' => 'murid',
        ]);

        $student = Murid::create([
            'user_id' => $studentUser->id,
            'nis' => '22001',
            'nama_lengkap' => 'Student A',
            'jenis_kelamin' => 'L',
            'kelas_id' => $this->kelas->id,
            'angkatan' => 2022,
            'status' => 'aktif',
        ]);

        // Register first
        $student->snbpPendaftar()->create();

        // Withdraw
        $response = $this->actingAs($studentUser)
            ->post(route('murid.snbp.batal'));

        $response->assertRedirect(route('murid.snbp.index'));
        $response->assertSessionHas('success');
        $this->assertFalse($student->snbpPendaftar()->exists());
    }

    public function test_murid_cannot_register_if_snbp_menu_is_hidden(): void
    {
        Setting::set('snbp_menu_status', 'nonaktif');
        Setting::set('snbp_deadline', now()->addDays(2)->toIso8601String());

        $studentUser = User::create([
            'name' => 'Student A',
            'username' => '22001',
            'password' => Hash::make('password'),
            'role' => 'murid',
        ]);

        $student = Murid::create([
            'user_id' => $studentUser->id,
            'nis' => '22001',
            'nama_lengkap' => 'Student A',
            'jenis_kelamin' => 'L',
            'kelas_id' => $this->kelas->id,
            'angkatan' => 2022,
            'status' => 'aktif',
        ]);

        $response = $this->actingAs($studentUser)
            ->get(route('murid.snbp.index'));
        $response->assertStatus(404);

        $response = $this->actingAs($studentUser)
            ->post(route('murid.snbp.daftar'));
        $response->assertStatus(403);
    }

    public function test_murid_cannot_register_after_deadline(): void
    {
        Setting::set('snbp_menu_status', 'aktif');
        Setting::set('snbp_deadline', now()->subMinutes(1)->toIso8601String());

        $studentUser = User::create([
            'name' => 'Student A',
            'username' => '22001',
            'password' => Hash::make('password'),
            'role' => 'murid',
        ]);

        $student = Murid::create([
            'user_id' => $studentUser->id,
            'nis' => '22001',
            'nama_lengkap' => 'Student A',
            'jenis_kelamin' => 'L',
            'kelas_id' => $this->kelas->id,
            'angkatan' => 2022,
            'status' => 'aktif',
        ]);

        $response = $this->actingAs($studentUser)
            ->post(route('murid.snbp.daftar'));

        $response->assertSessionHas('error');
        $this->assertFalse($student->snbpPendaftar()->exists());
    }

    public function test_snbp_ranking_ignores_semester_6_grades(): void
    {
        // 1. Create two students
        $students = [];
        for ($i = 1; $i <= 2; $i++) {
            $user = User::create([
                'name' => "Student $i",
                'username' => "2200$i",
                'password' => Hash::make('password'),
                'role' => 'murid',
            ]);

            $student = Murid::create([
                'user_id' => $user->id,
                'nis' => "2200$i",
                'nama_lengkap' => "Student $i",
                'jenis_kelamin' => 'L',
                'kelas_id' => $this->kelas->id,
                'angkatan' => 2022,
                'status' => 'aktif',
            ]);

            $student->snbpPendaftar()->create();
            $students[$i] = $student;
        }

        // 2. Add grades:
        // Student 1: 80.00 for semesters 1-5, and 100.00 for semester 6
        for ($s = 1; $s <= 5; $s++) {
            Nilai::create([
                'murid_id' => $students[1]->id,
                'semester_id' => $this->semesters[$s]->id,
                'mata_pelajaran_id' => $this->mapel->id,
                'nilai' => 80.00,
            ]);
        }
        Nilai::create([
            'murid_id' => $students[1]->id,
            'semester_id' => $this->semesters[6]->id,
            'mata_pelajaran_id' => $this->mapel->id,
            'nilai' => 100.00,
        ]);

        // Student 2: 85.00 for semesters 1-5, and 60.00 for semester 6
        for ($s = 1; $s <= 5; $s++) {
            Nilai::create([
                'murid_id' => $students[2]->id,
                'semester_id' => $this->semesters[$s]->id,
                'mata_pelajaran_id' => $this->mapel->id,
                'nilai' => 85.00,
            ]);
        }
        Nilai::create([
            'murid_id' => $students[2]->id,
            'semester_id' => $this->semesters[6]->id,
            'mata_pelajaran_id' => $this->mapel->id,
            'nilai' => 60.00,
        ]);

        // 3. Fetch ranking leaderboard from RankingEngine
        $rankingEngine = app(\App\Services\RankingEngine::class);
        $rankings = $rankingEngine->getSnbpRankingList($this->jurusan->id);

        $this->assertCount(2, $rankings);
        $this->assertEquals($students[2]->id, $rankings[0]->murid_id);
        $this->assertEquals(1, $rankings[0]->rank_snbp);
        $this->assertEquals(85.00, $rankings[0]->avg_nilai);

        $this->assertEquals($students[1]->id, $rankings[1]->murid_id);
        $this->assertEquals(2, $rankings[1]->rank_snbp);
        $this->assertEquals(80.00, $rankings[1]->avg_nilai);
    }
}
