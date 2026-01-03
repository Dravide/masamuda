<?php

namespace Tests\Feature;

use App\Livewire\Admin\School\Index;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class SchoolManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_school_page_contains_livewire_component()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get(route('admin.sekolah.index'))
            ->assertSuccessful()
            ->assertSeeLivewire(Index::class);
    }

    public function test_can_create_new_school()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Storage::fake('public');
        $logo = UploadedFile::fake()->image('logo.jpg');

        Livewire::actingAs($admin)
            ->test(Index::class)
            ->set('npsn', '12345678')
            ->set('name', 'SMK Negeri 1 Contoh')
            ->set('status', 'negeri')
            ->set('logo', $logo)
            ->set('address', 'Jl. Pendidikan No. 1')
            ->set('district', 'Kecamatan Contoh')
            ->set('city', 'Kota Contoh')
            ->set('province', 'Provinsi Contoh')
            ->set('postal_code', '12345')
            ->set('rt_rw', '01/02')
            ->set('email', 'info@smkn1.sch.id')
            ->call('store')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('schools', [
            'npsn' => '12345678',
            'name' => 'SMK Negeri 1 Contoh',
            'status' => 'negeri',
            'email' => 'info@smkn1.sch.id',
        ]);
    }

    public function test_npsn_validation()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        Livewire::actingAs($admin)
            ->test(Index::class)
            ->set('npsn', '123') // Invalid length
            ->set('name', 'Test School')
            ->call('store')
            ->assertHasErrors(['npsn']);
    }

    public function test_can_update_school()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $school = School::create([
            'npsn' => '87654321',
            'name' => 'Old Name',
            'status' => 'swasta',
            'address' => 'Old Address',
            'district' => 'District',
            'city' => 'City',
            'province' => 'Province',
            'postal_code' => '54321',
        ]);

        Livewire::actingAs($admin)
            ->test(Index::class)
            ->call('edit', $school->id)
            ->set('name', 'New Name')
            ->call('update')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('schools', [
            'id' => $school->id,
            'name' => 'New Name',
        ]);
    }

    public function test_can_delete_school()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $school = School::create([
            'npsn' => '11223344',
            'name' => 'School to Delete',
            'status' => 'negeri',
            'address' => 'Address',
            'district' => 'District',
            'city' => 'City',
            'province' => 'Province',
            'postal_code' => '11111',
        ]);

        Livewire::actingAs($admin)
            ->test(Index::class)
            ->call('delete', $school->id);

        $this->assertDatabaseMissing('schools', ['id' => $school->id]);
    }

    public function test_can_filter_schools()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        School::create([
            'npsn' => '11111111',
            'name' => 'Sekolah Negeri',
            'status' => 'negeri',
            'address' => 'Address',
            'district' => 'District',
            'city' => 'City',
            'province' => 'Province',
            'postal_code' => '11111',
        ]);
        School::create([
            'npsn' => '22222222',
            'name' => 'Sekolah Swasta',
            'status' => 'swasta',
            'address' => 'Address',
            'district' => 'District',
            'city' => 'City',
            'province' => 'Province',
            'postal_code' => '22222',
        ]);

        Livewire::actingAs($admin)
            ->test(Index::class)
            ->set('statusFilter', 'negeri')
            ->assertSee('Sekolah Negeri')
            ->assertDontSee('Sekolah Swasta');
    }
}
