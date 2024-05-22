<?php

use Laravel\Socialite\Contracts\Provider;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User;
use Mockery\MockInterface;

test('can authenticate using socialite github provider', function () {
    $providerMock = getProviderMock();
    $providerUserMock = getProviderUserMock();
    $providerMock->shouldReceive('user')->andReturn($providerUserMock);

    Socialite::shouldReceive('driver')->with('github')->andReturn($providerMock);

    $response = $this->get('/auth/github/callback');
    $response->assertRedirect();
});

test('can authenticate using socialite google provider', function () {
    $provider = getProviderMock();
    $provider->shouldReceive('user')->andReturn(getProviderUserMock());

    Socialite::shouldReceive('driver')->with('google')->andReturn($provider);
    $response = $this->get('/auth/google/callback');
    $response->assertRedirect();
});


function getProviderUserMock(): User&MockInterface
{
    $providerUser = Mockery::mock('Laravel\Socialite\Two\User');
    return $providerUser->shouldReceive('getId')
        ->andReturn(1234567890)
        ->shouldReceive('getEmail')
        ->andReturn('socialte@example.com')
        ->shouldReceive('getNickname')
        ->andReturn('socialite')
        ->shouldReceive('getName')
        ->andReturn('Socialite User')
        ->shouldReceive('getAvatar')
        ->andReturn('https://en.gravatar.com/userimage');
}

function getProviderMock(): Provider&MockInterface
{
    return Mockery::mock('Laravel\Socialite\Contracts\Provider');
}
