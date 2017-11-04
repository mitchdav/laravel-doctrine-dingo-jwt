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
            'default'=>[
                'read'=>[
                    'query'=>[
                        'select'=>[
                            'standardSelect'=>'partial u.{id, name, address, job}'
                        ],
                        'where'=>[
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