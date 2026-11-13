<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_every_role_can_open_profile_page(): void
    {
        foreach (['admin', 'pemilik', 'penyewa'] as $role) {
            $user = User::factory()->create(['role' => $role, 'name' => "User {$role}"]);

            $this->actingAs($user)->get(route('profile.edit'))
                ->assertStatus(200)
                ->assertSee('Profil Saya')
                ->assertSee("User {$role}");
        }
    }

    public function test_guest_cannot_open_profile_page(): void
    {
        $this->get(route('profile.edit'))->assertRedirect('/login');
    }

    public function test_user_can_update_profile_data(): void
    {
        $user = User::factory()->create(['role' => 'penyewa']);

        $this->actingAs($user)->put(route('profile.update'), [
            'name' => 'Nama Baru',
            'email' => 'baru@kos.com',
            'nomor_hp' => '089999999999',
        ])->assertRedirect(route('profile.edit'));

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Nama Baru',
            'email' => 'baru@kos.com',
            'nomor_hp' => '089999999999',
        ]);
    }

    public function test_email_must_stay_unique(): void
    {
        User::factory()->create(['email' => 'dipakai@kos.com']);
        $user = User::factory()->create(['role' => 'pemilik']);

        $this->actingAs($user)->put(route('profile.update'), [
            'name' => $user->name,
            'email' => 'dipakai@kos.com',
        ])->assertSessionHasErrors('email');
    }

    public function test_password_change_requires_current_password(): void
    {
        $user = User::factory()->create(['role' => 'pemilik', 'password' => Hash::make('lama12345')]);

        $this->actingAs($user)->put(route('profile.password'), [
            'current_password' => 'salah123',
            'password' => 'baru12345',
            'password_confirmation' => 'baru12345',
        ])->assertSessionHasErrors('current_password');

        $this->actingAs($user)->put(route('profile.password'), [
            'current_password' => 'lama12345',
            'password' => 'baru12345',
            'password_confirmation' => 'baru12345',
        ])->assertRedirect(route('profile.edit'));

        $this->assertTrue(Hash::check('baru12345', $user->fresh()->password));
    }
}

