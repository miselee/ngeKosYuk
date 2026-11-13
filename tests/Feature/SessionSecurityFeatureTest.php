<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class SessionSecurityFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_pages_return_strict_no_cache_headers(): void
    {
        $routesByRole = [
            'admin' => ['/profil', '/admin/dashboard'],
            'pemilik' => ['/profil', '/pemilik/dashboard'],
            'penyewa' => ['/profil', '/penyewa/dashboard'],
        ];

        foreach ($routesByRole as $role => $paths) {
            $user = User::factory()->create(['role' => $role]);

            foreach ($paths as $path) {
                $response = $this->actingAs($user)->get($path);

                $response->assertStatus(200);
                $response->assertHeader('Pragma', 'no-cache');
                $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
                $response->assertHeader('X-Content-Type-Options', 'nosniff');

                $cacheControl = (string) $response->headers->get('Cache-Control');
                $this->assertStringContainsString('no-store', $cacheControl);
                $this->assertStringContainsString('no-cache', $cacheControl);
                $this->assertStringContainsString('must-revalidate', $cacheControl);
                $this->assertStringContainsString('max-age=0', $cacheControl);
            }
        }
    }

    public function test_logout_invalidates_session_and_blocks_subsequent_profile_access(): void
    {
        $user = User::factory()->create(['role' => 'penyewa']);

        $this->actingAs($user)->get('/profil')->assertStatus(200);
        $oldSessionId = session()->getId();

        $logoutResponse = $this->post('/logout');
        $logoutResponse->assertRedirect('/login');
        $logoutResponse->assertCookieExpired(Auth::guard('web')->getRecallerName());

        $this->assertNotSame($oldSessionId, session()->getId());
        $this->assertGuest();

        $this->get('/profil')->assertRedirect('/login');
        $this->get('/penyewa/dashboard')->assertRedirect('/login');
    }

    public function test_session_check_endpoint_reflects_auth_state(): void
    {
        $this->getJson(route('auth.session-check'))
            ->assertStatus(200)
            ->assertJson(['authenticated' => false]);

        $user = User::factory()->create(['role' => 'pemilik']);

        $this->actingAs($user)->getJson(route('auth.session-check'))
            ->assertStatus(200)
            ->assertJson(['authenticated' => true]);

        $this->post('/logout')->assertRedirect('/login');

        $this->getJson(route('auth.session-check'))
            ->assertStatus(200)
            ->assertJson(['authenticated' => false]);
    }

    public function test_authenticated_users_are_redirected_away_from_login_and_register(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $pemilik = User::factory()->create(['role' => 'pemilik']);
        $penyewa = User::factory()->create(['role' => 'penyewa']);

        $this->actingAs($admin)->get('/login')->assertRedirect('/admin/dashboard');
        $this->actingAs($admin)->get('/register')->assertRedirect('/admin/dashboard');

        $this->actingAs($pemilik)->get('/login')->assertRedirect('/pemilik/dashboard');
        $this->actingAs($pemilik)->get('/register')->assertRedirect('/pemilik/dashboard');

        $this->actingAs($penyewa)->get('/login')->assertRedirect('/penyewa/dashboard');
        $this->actingAs($penyewa)->get('/register')->assertRedirect('/penyewa/dashboard');
    }
}
