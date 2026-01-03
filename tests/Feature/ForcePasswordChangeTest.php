<?php

namespace Tests\Feature;

use App\Livewire\Sekolah\Dashboard;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class ForcePasswordChangeTest extends TestCase
{
    use RefreshDatabase;

    // public function test_user_is_redirected_to_dashboard_if_password_change_required()
    // {
    //     $user = User::factory()->create([
    //         'role' => 'sekolah',
    //         'password_change_required' => true,
    //     ]);

    //     $this->actingAs($user)
    //         ->get('/sekolah/some-other-page')
    //         ->assertRedirect(route('sekolah.dashboard'));
    // }

    public function test_modal_is_shown_on_dashboard()
    {
        $user = User::factory()->create([
            'role' => 'sekolah',
            'password_change_required' => true,
        ]);

        Livewire::actingAs($user)
            ->test(Dashboard::class)
            ->assertSet('showPasswordChangeModal', true)
            ->assertSee('Ganti Password Wajib');
    }

    public function test_password_validation_rules()
    {
        $user = User::factory()->create([
            'role' => 'sekolah',
            'password_change_required' => true,
            'password' => Hash::make('old_password'),
        ]);

        Livewire::actingAs($user)
            ->test(Dashboard::class)
            ->set('current_password', 'wrong_password')
            ->set('password', 'new_password')
            ->set('password_confirmation', 'new_password')
            ->call('updatePassword')
            ->assertHasErrors(['current_password']);

        Livewire::actingAs($user)
            ->test(Dashboard::class)
            ->set('current_password', 'old_password')
            ->set('password', 'weak') // Too short
            ->set('password_confirmation', 'weak')
            ->call('updatePassword')
            ->assertHasErrors(['password']);

        Livewire::actingAs($user)
            ->test(Dashboard::class)
            ->set('current_password', 'old_password')
            ->set('password', 'nouppercase1') // No uppercase
            ->set('password_confirmation', 'nouppercase1')
            ->call('updatePassword')
            ->assertHasErrors(['password']);
            
        Livewire::actingAs($user)
            ->test(Dashboard::class)
            ->set('current_password', 'old_password')
            ->set('password', 'old_password') // Same as old
            ->set('password_confirmation', 'old_password')
            ->call('updatePassword')
            ->assertHasErrors(['password']);
    }

    public function test_successful_password_change()
    {
        $user = User::factory()->create([
            'role' => 'sekolah',
            'password_change_required' => true,
            'password' => Hash::make('OldPassword123'),
        ]);

        Livewire::actingAs($user)
            ->test(Dashboard::class)
            ->set('current_password', 'OldPassword123')
            ->set('password', 'NewPassword123')
            ->set('password_confirmation', 'NewPassword123')
            ->call('updatePassword')
            ->assertHasNoErrors()
            ->assertSet('showPasswordChangeModal', false);

        $user->refresh();
        $this->assertFalse($user->password_change_required);
        $this->assertTrue(Hash::check('NewPassword123', $user->password));
    }
}
