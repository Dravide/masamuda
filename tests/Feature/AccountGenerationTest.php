<?php

namespace Tests\Feature;

use App\Livewire\Admin\School\Index;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class AccountGenerationTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_generate_school_account()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $school = School::create([
            'npsn' => '12345678',
            'name' => 'Test School',
            'status' => 'negeri',
            'address' => 'Test Address',
            'district' => 'District',
            'city' => 'City',
            'province' => 'Province',
            'postal_code' => '12345',
        ]);

        Livewire::actingAs($admin)
            ->test(Index::class)
            ->call('generateAccount', $school->id)
            ->assertDispatched('alert');

        $this->assertDatabaseHas('users', [
            'username' => '12345678',
            'email' => '12345678@masamudastudio.id',
            'role' => 'sekolah',
            'password_change_required' => true,
        ]);

        $user = User::where('username', '12345678')->first();
        $this->assertTrue(Hash::check('12345678', $user->password));
        
        $school->refresh();
        $this->assertEquals($user->id, $school->user_id);
    }

    public function test_cannot_generate_duplicate_account()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $school = School::create([
            'npsn' => '87654321',
            'name' => 'Test School 2',
            'status' => 'swasta',
            'address' => 'Address',
            'district' => 'District',
            'city' => 'City',
            'province' => 'Province',
            'postal_code' => '54321',
        ]);

        // Create user manually first to simulate conflict
        User::create([
            'name' => 'Existing User',
            'username' => '87654321',
            'email' => '87654321@masamudastudio.id',
            'password' => Hash::make('password'),
            'role' => 'sekolah',
        ]);

        Livewire::actingAs($admin)
            ->test(Index::class)
            ->call('generateAccount', $school->id)
            ->assertDispatched('alert');
    }

    public function test_force_password_change_middleware()
    {
        $user = User::factory()->create([
            'role' => 'sekolah',
            'password_change_required' => true,
        ]);

        $this->actingAs($user)
            ->get(route('sekolah.dashboard'))
            ->assertSuccessful()
            ->assertSee('Ganti Password Wajib'); // Now checks for modal content
    }
}
