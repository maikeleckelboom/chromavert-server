<?php

use Laravel\Socialite\Facades\Socialite;


test('can authenticate using github provider', function () {
    $abstractUser = Mockery::mock('Laravel\Socialite\Two\User');
    $abstractUser
        ->shouldReceive('getId')
        ->andReturn(1234567890)
        ->shouldReceive('getEmail')
        ->andReturn('socialte@example.com')
        ->shouldReceive('getNickname')
        ->andReturn('socialite')
        ->shouldReceive('getName')
        ->andReturn('Socialite User')
        ->shouldReceive('getAvatar')
        ->andReturn('https://en.gravatar.com/userimage');
    $provider = Mockery::mock('Laravel\Socialite\Contracts\Provider');
    $provider->shouldReceive('user')->andReturn($abstractUser);
    Socialite::shouldReceive('driver')->with('github')->andReturn($provider);
    $response = $this->get('/auth/github/callback');
    $response->assertRedirect();
});

test('can authenticate using google provider', function () {
    $abstractUser = Mockery::mock('Laravel\Socialite\Two\User');
    $abstractUser
        ->shouldReceive('getId')
        ->andReturn(1234567890)
        ->shouldReceive('getEmail')
        ->andReturn('socialte@example.com')
        ->shouldReceive('getNickname')
        ->andReturn('socialite')
        ->shouldReceive('getName')
        ->andReturn('Socialite User')
        ->shouldReceive('getAvatar')
        ->andReturn('https://en.gravatar.com/userimage');
    $provider = Mockery::mock('Laravel\Socialite\Contracts\Provider');
    $provider->shouldReceive('user')->andReturn($abstractUser);
    Socialite::shouldReceive('driver')->with('google')->andReturn($provider);
    $response = $this->get('/auth/google/callback');
    $response->assertRedirect();
});
