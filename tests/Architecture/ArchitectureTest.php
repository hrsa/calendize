<?php

arch('enums are fome')
    ->expect('App\Enums')
    ->toBeEnums();

arch('models are correct')
    ->expect('App\Models')
    ->toExtend(\Illuminate\Database\Eloquent\Model::class);

arch('ray is not used')
    ->expect('ray')
    ->not->toBeUsed();

arch('debugging functions are not used')
    ->expect(['dd', 'dump', 'ray'])
    ->each->not->toBeUsed();
