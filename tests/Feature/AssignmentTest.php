<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssignmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_for_assignment_routes(): void
    {
        $this->get('/assignments')->assertRedirect('/login');
        $this->get('/assignments/create')->assertRedirect('/login');
        $this->post('/assignments', [
            'title' => 'Tugas 1',
            'course_name' => 'PAW',
            'due_at' => '2026-12-01T23:59',
            'status' => 'belum_dikerjakan',
        ])->assertRedirect('/login');
    }

    public function test_authenticated_user_can_create_read_update_and_delete_assignments(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/assignments')
            ->assertOk()
            ->assertSee('Belum ada tugas kuliah');

        $this->actingAs($user)
            ->post('/assignments', [
                'title' => 'Laporan Praktikum PAW',
                'course_name' => 'Pengembangan Aplikasi Web',
                'description' => 'Implementasi CRUD tugas kuliah.',
                'due_at' => '2026-12-10T23:59',
                'status' => 'belum_dikerjakan',
            ])
            ->assertRedirect('/assignments')
            ->assertSessionHas('status', 'Tugas kuliah berhasil ditambahkan.');

        $assignment = Assignment::firstOrFail();
        $this->assertSame($user->id, $assignment->user_id);
        $this->assertSame('Laporan Praktikum PAW', $assignment->title);
        $this->assertSame('belum_dikerjakan', $assignment->status);

        $this->actingAs($user)
            ->get('/assignments')
            ->assertOk()
            ->assertSee('Laporan Praktikum PAW')
            ->assertSee('Pengembangan Aplikasi Web')
            ->assertSee('Belum dikerjakan');

        $this->actingAs($user)
            ->get('/assignments/'.$assignment->id.'/edit')
            ->assertOk()
            ->assertSee('Ubah tugas kuliah')
            ->assertSee('Laporan Praktikum PAW');

        $this->actingAs($user)
            ->put('/assignments/'.$assignment->id, [
                'title' => 'Laporan Praktikum PAW Revisi',
                'course_name' => 'Pengembangan Aplikasi Web',
                'description' => 'Sudah ditambah unit test.',
                'due_at' => '2026-12-12T23:59',
                'status' => 'dikerjakan',
            ])
            ->assertRedirect('/assignments')
            ->assertSessionHas('status', 'Tugas kuliah berhasil diperbarui.');

        $this->assertDatabaseHas('assignments', [
            'id' => $assignment->id,
            'title' => 'Laporan Praktikum PAW Revisi',
            'status' => 'dikerjakan',
        ]);

        $this->actingAs($user)
            ->patch('/assignments/'.$assignment->id.'/status', [
                'status' => 'selesai',
            ])
            ->assertRedirect('/assignments')
            ->assertSessionHas('status');

        $this->assertDatabaseHas('assignments', [
            'id' => $assignment->id,
            'status' => 'selesai',
        ]);

        $this->actingAs($user)
            ->delete('/assignments/'.$assignment->id)
            ->assertRedirect('/assignments')
            ->assertSessionHas('status', 'Tugas kuliah berhasil dihapus.');

        $this->assertDatabaseCount('assignments', 0);
    }

    public function test_assignments_are_sorted_by_nearest_deadline_and_can_be_filtered(): void
    {
        $user = User::factory()->create();

        Assignment::create([
            'user_id' => $user->id,
            'title' => 'Tugas Paling Lama',
            'course_name' => 'Basis Data',
            'due_at' => '2026-12-20 23:59:00',
            'status' => 'belum_dikerjakan',
        ]);

        Assignment::create([
            'user_id' => $user->id,
            'title' => 'Tugas Paling Dekat',
            'course_name' => 'Pemrograman Web',
            'due_at' => '2026-12-01 12:00:00',
            'status' => 'dikerjakan',
        ]);

        Assignment::create([
            'user_id' => $user->id,
            'title' => 'Tugas Tengah',
            'course_name' => 'Jaringan Komputer',
            'due_at' => '2026-12-10 18:00:00',
            'status' => 'selesai',
        ]);

        $this->actingAs($user)
            ->get('/assignments')
            ->assertOk()
            ->assertSeeTextInOrder([
                'Tugas Paling Dekat',
                'Tugas Tengah',
                'Tugas Paling Lama',
            ]);

        $this->actingAs($user)
            ->get('/assignments?status=selesai')
            ->assertOk()
            ->assertSee('Tugas Tengah')
            ->assertDontSee('Tugas Paling Dekat')
            ->assertDontSee('Tugas Paling Lama');

        $this->actingAs($user)
            ->get('/assignments?q=Basis')
            ->assertOk()
            ->assertSee('Tugas Paling Lama')
            ->assertDontSee('Tugas Paling Dekat');
    }

    public function test_assignments_are_isolated_per_user(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();

        $assignment = Assignment::create([
            'user_id' => $owner->id,
            'title' => 'Tugas Rahasia Owner',
            'course_name' => 'Keamanan Informasi',
            'due_at' => '2026-12-05 23:59:00',
            'status' => 'belum_dikerjakan',
        ]);

        $this->actingAs($other)
            ->get('/assignments')
            ->assertOk()
            ->assertDontSee('Tugas Rahasia Owner');

        $this->actingAs($other)
            ->get('/assignments/'.$assignment->id.'/edit')
            ->assertNotFound();

        $this->actingAs($other)
            ->put('/assignments/'.$assignment->id, [
                'title' => 'Dibajak',
                'course_name' => 'Keamanan Informasi',
                'due_at' => '2026-12-05T23:59',
                'status' => 'selesai',
            ])
            ->assertNotFound();

        $this->actingAs($other)
            ->patch('/assignments/'.$assignment->id.'/status', [
                'status' => 'selesai',
            ])
            ->assertNotFound();

        $this->actingAs($other)
            ->delete('/assignments/'.$assignment->id)
            ->assertNotFound();

        $this->assertDatabaseHas('assignments', [
            'id' => $assignment->id,
            'title' => 'Tugas Rahasia Owner',
            'status' => 'belum_dikerjakan',
        ]);
    }

    public function test_validation_rejects_invalid_or_missing_assignment_data(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/assignments', [
                'title' => '',
                'course_name' => '',
                'due_at' => 'bukan-tanggal',
                'status' => 'status_ngawur',
            ])
            ->assertSessionHasErrors(['title', 'course_name', 'due_at', 'status']);

        $this->assertDatabaseCount('assignments', 0);
    }
}
