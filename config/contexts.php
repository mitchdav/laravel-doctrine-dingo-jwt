<?php

return [
	'guest' => [
	    'description' => 'Analogous to accessing a resource as a user with guest access level.',
        'ownsResources'=>[
            'albums'=>[],
            'artists'=>[
                '{artist}'=>[
                    'albums'=>[]
                ]
            ],
        ],
    ],
    'user' => [
        'description' => 'Analogous to accessing a resource as a user with standard user access level.',
        'ownsResources'=>[
            'albums'=>[],
            'artists'=>[],
            'users'=>[
                '{user}'=>[
                    'albums'=>[]
                ]
            ],
        ],
    ],
    'admin' => [
        'description' => 'Analogous to accessing a resource as a user with admin access level.',
        'ownsResources'=>[
            'albums'=>[],
            'artists'=>[],
            'users'=>[
                '{user}'=>[
                    'albums'=>[]
                ]
            ],
        ]
    ],
    'super-admin' => [
        'description' => 'Analogous to accessing a resource as a user with super-admin access level.',
        'ownsResources'=>[
            'users'=>[],
            'permissions'=>[],
            'roles'=>[],
        ]
    ],
];
