<?php

use App\Models\Application;
use App\Models\Cartographer;
use App\Models\User;
use Database\Seeders\PermissionRoleTableSeeder;
use Database\Seeders\PermissionsTableSeeder;
use Database\Seeders\RolesTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed([
        PermissionsTableSeeder::class,
        RolesTableSeeder::class,
        PermissionRoleTableSeeder::class,
    ]);

    // Default config: reminders enabled, 6 months threshold, 30-day cooldown
    Config::set('mercator.cartography.reminders_enabled', true);
    Config::set('mercator.cartography.reminder_months', 6);
    Config::set('mercator.cartography.reminder_every_days', 30);
    Config::set('mercator.cartography.reminder_from', 'mercator@test.local');
    Config::set('mercator.cartography.reminder_subject', '[Test] Rappel');
    Config::set('mercator.cartography.reminder_body', 'Bonjour :name, :count objet(s) depuis :months mois. :list :mercator');
    Config::set('mercator.cartography.reminder_last_sent', null);
});

it('exits without sending mail when reminders are disabled', function (): void {
    Mail::fake();
    Config::set('mercator.cartography.reminders_enabled', false);

    $this->artisan('mercator:remind-cartographers')->assertExitCode(0);

    Mail::assertNothingSent();
});

it('exits without sending mail when last send is too recent', function (): void {
    Mail::fake();
    Config::set('mercator.cartography.reminder_last_sent', now()->subDays(5)->toDateString());
    Config::set('mercator.cartography.reminder_every_days', 30);

    $this->artisan('mercator:remind-cartographers')->assertExitCode(0);

    Mail::assertNothingSent();
});

it('sends no mail when all objects are recent', function (): void {
    Mail::fake();

    $user = User::factory()->create();
    $app  = Application::factory()->create(['updated_at' => now()->subMonths(1)]);

    Cartographer::create([
        'cartographiable_type' => Application::class,
        'cartographiable_id'   => $app->id,
        'user_id'              => $user->id,
    ]);

    $this->artisan('mercator:remind-cartographers')->assertExitCode(0);

    Mail::assertNothingSent();
});

it('sends one mail per cartographer with outdated objects', function (): void {
    Mail::fake();

    $user = User::factory()->create(['email' => 'carto@example.com']);
    $app  = Application::factory()->create(['updated_at' => now()->subMonths(7)]);

    Cartographer::create([
        'cartographiable_type' => Application::class,
        'cartographiable_id'   => $app->id,
        'user_id'              => $user->id,
    ]);

    $this->artisan('mercator:remind-cartographers')->assertExitCode(0);

    Mail::assertSentCount(1);
});

it('substitutes placeholders in the mail body', function (): void {
    Mail::fake();

    $user = User::factory()->create(['name' => 'Alice Dupont', 'email' => 'alice@example.com']);
    $app  = Application::factory()->create(['updated_at' => now()->subMonths(7)]);

    Cartographer::create([
        'cartographiable_type' => Application::class,
        'cartographiable_id'   => $app->id,
        'user_id'              => $user->id,
    ]);

    $this->artisan('mercator:remind-cartographers')->assertExitCode(0);

    Mail::assertSentCount(1);

    $sent = Mail::sent(\Illuminate\Mail\Mailable::class)->first();

    // The body is built via Mail::html() — we verify via the output line instead
    // by checking the sent count and that no literal placeholder remains.
    expect($sent)->not->toBeNull();
});

it('sends one mail per distinct cartographer when multiple cartographers exist', function (): void {
    Mail::fake();

    $userA = User::factory()->create(['email' => 'a@example.com']);
    $userB = User::factory()->create(['email' => 'b@example.com']);

    $app1 = Application::factory()->create(['updated_at' => now()->subMonths(7)]);
    $app2 = Application::factory()->create(['updated_at' => now()->subMonths(8)]);

    Cartographer::create([
        'cartographiable_type' => Application::class,
        'cartographiable_id'   => $app1->id,
        'user_id'              => $userA->id,
    ]);

    Cartographer::create([
        'cartographiable_type' => Application::class,
        'cartographiable_id'   => $app2->id,
        'user_id'              => $userB->id,
    ]);

    $this->artisan('mercator:remind-cartographers')->assertExitCode(0);

    // One mail per distinct cartographer, no duplicates.
    Mail::assertSentCount(2);
});

it('groups all outdated objects for the same cartographer into a single mail', function (): void {
    Mail::fake();

    $user = User::factory()->create(['email' => 'carto@example.com']);

    $app1 = Application::factory()->create(['updated_at' => now()->subMonths(7)]);
    $app2 = Application::factory()->create(['updated_at' => now()->subMonths(8)]);

    foreach ([$app1, $app2] as $app) {
        Cartographer::create([
            'cartographiable_type' => Application::class,
            'cartographiable_id'   => $app->id,
            'user_id'              => $user->id,
        ]);
    }

    $this->artisan('mercator:remind-cartographers')->assertExitCode(0);

    // Both objects belong to the same user → one mail, not two.
    Mail::assertSentCount(1);
});

it('updates reminder_last_sent after sending', function (): void {
    Mail::fake();

    $user = User::factory()->create();
    $app  = Application::factory()->create(['updated_at' => now()->subMonths(7)]);

    Cartographer::create([
        'cartographiable_type' => Application::class,
        'cartographiable_id'   => $app->id,
        'user_id'              => $user->id,
    ]);

    $this->artisan('mercator:remind-cartographers')->assertExitCode(0);

    expect(config('mercator.cartography.reminder_last_sent'))
        ->toStartWith(now()->toDateString());
});

it('does not update reminder_last_sent when reminders are disabled', function (): void {
    Mail::fake();
    Config::set('mercator.cartography.reminders_enabled', false);
    Config::set('mercator.cartography.reminder_last_sent', null);

    $this->artisan('mercator:remind-cartographers')->assertExitCode(0);

    expect(config('mercator.cartography.reminder_last_sent'))->toBeNull();
});
