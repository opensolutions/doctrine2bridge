<?php

// Assuming we have set the following in app/config/auth.php:
//
// 'driver' => 'doctrine2bridge'

// this assumes no namespace:

Auth::extend( 'doctrine2bridge', function()
{
    return new \Illuminate\Auth\Guard(
        new \Doctrine2Bridge\Auth\Doctrine2UserProvider(
            D2EM::getRepository( '\Entities\User' ),
            new \Illuminate\Hashing\BcryptHasher
        ),
        App::make('session.store')
    );
});

                                                        