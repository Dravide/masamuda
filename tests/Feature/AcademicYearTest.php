<?php

namespace Tests\Feature;

use App\Livewire\Admin\AcademicYear\Index;
use App\Models\AcademicYear;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AcademicYearTest extends TestCase
{
    use RefreshDatabase;

    public function test_academic_year_page_contains_livewire_component()
    {
        $user = User::factory()->create(['role' => 'admin']);

        $this->actingAs($user)
            ->get(route('admin.tahun-pelajaran.index'))
            ->assertSuccessful()
            ->assertSeeLivewire(Index::class);
    }

    public function test_can_create_academic_year()
    {
        $user = User::factory()->create(['role' => 'admin']);

        Livewire::actingAs($user)
            ->test(Index::class)
            ->set('year_name', '2023/2024')
            ->set('start_date', '2023-07-01')
            ->set('end_date', '2023-12-31')
            ->set('semester', 'ganjil')
            ->set('is_active', true)
            ->call('store')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('academic_years', [
            'year_name' => '2023/2024',
            'semester' => 'ganjil',
            'is_active' => true,
        ]);
    }

    public function test_cannot_create_duplicate_academic_year()
    {
        $user = User::factory()->create(['role' => 'admin']);
        AcademicYear::create([
            'year_name' => '2023/2024',
            'start_date' => '2023-07-01',
            'end_date' => '2023-12-31',
            'semester' => 'ganjil',
            'is_active' => true,
        ]);

        Livewire::actingAs($user)
            ->test(Index::class)
            ->set('year_name', '2023/2024')
            ->set('start_date', '2023-07-01')
            ->set('end_date', '2023-12-31')
            ->set('semester', 'ganjil')
            ->call('store')
            ->assertHasErrors(['year_name']);
    }

    public function test_active_status_toggle_logic()
    {
        $user = User::factory()->create(['role' => 'admin']);
        
        // Create first active year
        $year1 = AcademicYear::create([
            'year_name' => '2023/2024',
            'start_date' => '2023-07-01',
            'end_date' => '2023-12-31',
            'semester' => 'ganjil',
            'is_active' => true,
        ]);

        // Create second active year via Livewire
        Livewire::actingAs($user)
            ->test(Index::class)
            ->set('year_name', '2023/2024')
            ->set('start_date', '2024-01-01')
            ->set('end_date', '2024-06-30')
            ->set('semester', 'genap')
            ->set('is_active', true)
            ->call('store');

        // Check if first year is deactivated
        $this->assertDatabaseHas('academic_years', [
            'id' => $year1->id,
            'is_active' => false,
        ]);

        $this->assertDatabaseHas('academic_years', [
            'year_name' => '2023/2024',
            'semester' => 'genap',
            'is_active' => true,
        ]);
    }

    public function test_cannot_delete_active_academic_year()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $year = AcademicYear::create([
            'year_name' => '2023/2024',
            'start_date' => '2023-07-01',
            'end_date' => '2023-12-31',
            'semester' => 'ganjil',
            'is_active' => true,
        ]);

        Livewire::actingAs($user)
            ->test(Index::class)
            ->call('delete', $year->id)
            ->assertSee('Tidak dapat menghapus tahun pelajaran yang sedang aktif');

        $this->assertDatabaseHas('academic_years', ['id' => $year->id]);
    }
}
