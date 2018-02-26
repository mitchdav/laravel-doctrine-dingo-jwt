<?php

namespace App\API\V1\Repositories;

use App\API\V1\Entities\User;
use App\Repositories\Repository;
use Doctrine\ORM\Query\Expr;
use TempestTools\Moat\Contracts\RepoHasPermissionsContract;
use TempestTools\Moat\Repository\HasPermissionsQueryTrait;
use TempestTools\Common\Constants\CommonArrayObjectKeyConstants;

/** @noinspection LongInheritanceChainInspection */
class UserRepository extends Repository implements RepoHasPermissionsContract
{
    use HasPermissionsQueryTrait;

	protected /** @noinspection ClassOverridesFieldOfSuperClassInspection */
        $entity = User::class;

    /**
     * @return array
     * @throws \RuntimeException
     */
    public function getTTConfig(): array
    {
        $expr = new Expr();
        /** @noinspection NullPointerExceptionInspection */
        return [
            // Default context that most requests fall back too, and other contexts inherit from.
            'default'=>[
                'read'=>[
                    'query'=>[
                        // Get just the id, name, address, job fields for read actions default.
                        'select'=>[
                            'standardSelect'=>'partial u.{id, name, address, job}'
                        ],
                        'where'=>[
                            // Only retrieve data about the currently logged in user by default.
                            'onlyCurrentUser'=>[
                                'type'=>'and',
                                'value'=>$expr->eq('u.id', $this->getArrayHelper()->parseArrayPath([CommonArrayObjectKeyConstants::USER_KEY_NAME, 'id']))
                            ]
                        ],
                    ],
                    'permissions'=>[
                        'where'=>[
                            'fields'=>[
                                'password'=>[
                                    'permissive'=>false
                                ]
                            ]
                        ]
                    ]
                ],

            ],
            'user'=>[
                'extends'=>[':default']
            ],
            'admin'=>[
                'extends'=>[':default'],
                'read'=>[
                    'query'=>[
                        // When in admin context, no longer only return info about the currently logged in user.
                        'where'=>[
                            'onlyCurrentUser'=>null
                        ],
                    ],
                ],
            ],
            // Below here is for testing purposes only
            'testing'=>[]
        ];
    }
}