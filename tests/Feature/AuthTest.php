<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;
use App\Livewire\Auth\Login;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_loads()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    public function test_user_can_login_as_siswa()
    {
        $user = User::factory()->create([
            'username' => 'siswa123',
            'password' => bcrypt('password'),
            'role' => 'siswa'
        ]);

        Livewire::test(Login::class)
            ->set('username', 'siswa123')
            ->set('password', 'password')
            ->call('login')
            ->assertRedirect(route('siswa.dashboard'));
            
        $this->assertAuthenticatedAs($user);
    }

    public function test_user_can_login_as_admin()
    {
        $user = User::factory()->create([
            'username' => 'admin123',
            'password' => bcrypt('password'),
            'role' => 'admin'
        ]);

        Livewire::test(Login::class)
            ->set('username', 'admin123')
            ->set('password', 'password')
            ->call('login')
            ->assertRedirect(route('admin.dashboard'));
            
        $this->assertAuthenticatedAs($user);
    }

    public function test_invalid_login_fails()
    {
        $user = User::factory()->create([
            'username' => 'siswa123',
            'password' => bcrypt('password'),
        ]);

        Livewire::test(Login::class)
            ->set('username', 'siswa123')
            ->set('password', 'wrong-password')
            ->call('login')
            ->assertHasErrors(['username']);
            
        $this->assertGuest();
    }

    public function test_validation_rules()
    {
        Livewire::test(Login::class)
            ->set('username', '')
            ->set('password', '')
            ->call('login')
            ->assertHasErrors(['username', 'password']);
            
        Livewire::test(Login::class)
            ->set('username', 'user space') // has space
            ->call('login')
            ->assertHasErrors(['username']);
    }

    public function test_role_access_control()
    {
        $siswa = User::factory()->create(['role' => 'siswa']);
        $admin = User::factory()->create(['role' => 'admin']);
        $sekolah = User::factory()->create(['role' => 'sekolah']);

        // Siswa accessing admin dashboard -> Forbidden
        $this->actingAs($siswa)
            ->get(route('admin.dashboard'))
            ->assertStatus(403);

        // Sekolah accessing admin dashboard -> Forbidden
        $this->actingAs($sekolah)
            ->get(route('admin.dashboard'))
            ->assertStatus(403);
            
        // Admin accessing siswa dashboard -> Allowed
        $this->actingAs($admin)
            ->get(route('siswa.dashboard'))
            ->assertStatus(200);
    }
}
