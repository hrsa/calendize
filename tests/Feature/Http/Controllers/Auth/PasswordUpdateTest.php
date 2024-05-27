<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

use function Pest\Laravel\actingAs;

test('password can be updated', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->from('/profile')
        ->put('/password', [
            'password'              => 'new-password',
            'password_confirmation' => 'new-password',
        ])->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    expect(Hash::check('new-password', $user->refresh()->password))->toBeTrue();
});
