<?php

namespace App\API\V1\Repositories;

use App\API\V1\Entities\Permission;
use App\Repositories\Repository;

/** @noinspection LongInheritanceChainInspection */
class PermissionRepository extends Repository
{
	protected /** @noinspection ClassOverridesFieldOfSuperClassInspection */
        $entity = Permission::class;

    /**
     * @return array
     */
    public function getTTConfig(): array
    {
        return [
            'default'=>[
                'read'=>[
                    'permissions'=>[
                        'allowed'=>false
                    ]
                ]
            ],
            'superAdmin'=>[
                'extends'=>[':default'],
                'read'=>[
                    'permissions'=>[
                        'allowed'=>true
                    ]
                ]
            ],
            // Below here is for testing purposes only
            'testing'=>[]
        ];
    }
}