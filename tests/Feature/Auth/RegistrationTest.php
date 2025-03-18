<?php

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users can register', function () {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'role' => 'donor',
        'phone' => '1234567890',
        'address' => '123 Test St, Test City, TS 12345',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('donor.dashboard', absolute: false));
});