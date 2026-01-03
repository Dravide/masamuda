<?php

namespace Tests\Feature;

use App\Livewire\Admin\User\Index;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_management_page_contains_livewire_component()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get(route('admin.pengguna.index'))
            ->assertSuccessful()
            ->assertSeeLivewire(Index::class);
    }

    public function test_can_create_new_user()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        Livewire::actingAs($admin)
            ->test(Index::class)
            ->set('name', 'New User')
            ->set('username', 'newuser')
            ->set('email', 'newuser@example.com')
            ->set('role', 'siswa')
            ->set('password', 'password')
            ->set('password_confirmation', 'password')
            ->set('is_active', true)
            ->call('store')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('users', [
            'username' => 'newuser',
            'email' => 'newuser@example.com',
            'role' => 'siswa',
        ]);
    }

    public function test_validation_works()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        Livewire::actingAs($admin)
            ->test(Index::class)
            ->set('name', '') // Required
            ->set('email', 'invalid-email') // Invalid email
            ->call('store')
            ->assertHasErrors(['name', 'email']);
    }

    public function test_can_search_users()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user1 = User::factory()->create(['name' => 'John Doe', 'email' => 'john@example.com']);
        $user2 = User::factory()->create(['name' => 'Jane Smith', 'email' => 'jane@example.com']);

        Livewire::actingAs($admin)
            ->test(Index::class)
            ->set('search', 'John')
            ->assertSee('John Doe')
            ->assertDontSee('Jane Smith');
    }

    public function test_can_filter_by_role()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $siswa = User::factory()->create(['role' => 'siswa', 'name' => 'Siswa User']);
        $sekolah = User::factory()->create(['role' => 'sekolah', 'name' => 'Sekolah User']);

        Livewire::actingAs($admin)
            ->test(Index::class)
            ->set('roleFilter', ['siswa'])
            ->assertSee('Siswa User')
            ->assertDontSee('Sekolah User');
    }

    public function test_cannot_delete_self()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        Livewire::actingAs($admin)
            ->test(Index::class)
            ->call('delete', $admin->id)
            ->assertSee('Tidak dapat menghapus akun sendiri');

        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }
}
