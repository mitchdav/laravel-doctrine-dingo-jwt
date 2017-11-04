<?php

namespace Database\Migrations;

use App\API\V1\Entities\Role;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema as Schema;
use TempestTools\Common\Doctrine\Utility\MakeEmTrait;

class Version20171003005708 extends AbstractMigration
{
    use MakeEmTrait;

    /**
     * @param Schema $schema
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function up(Schema $schema):void
    {
        $em = $this->em();
        $repo = $em->getRepository(Role::class);
        $repo->buildPermissions([
            'user'=>[
                'album',
                'album/{album}',
                'artist',
                'artist/{artist}',
                'user',
                'user/{user}',
            ],
            'admin'=>[
                'admin/album',
                'admin/album/{album}',
                'admin/artist',
                'admin/artist/{artist}',
                'admin/user',
                'admin/user/{user}',
            ],
            'super-admin'=>[
                'super-admin/user',
                'super-admin/user/{user}',
            ]
        ]);
    }

    /**
     * @param Schema $schema
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function down(Schema $schema):void
    {
        $em = $this->em();
        $repo = $em->getRepository(Role::class);
        $repo->removePermissions([
            'user'=>[
                'album',
                'album/{album}',
                'artist',
                'artist/{artist}',
                'user',
                'user/{user}',
            ],
            'admin'=>[
                'admin/album',
                'admin/album/{album}',
                'admin/artist',
                'admin/artist/{artist}',
                'admin/user',
                'admin/user/{user}',
            ],
            'super-admin'=>[
                'super-admin/user',
                'super-admin/user/{user}',
            ]
        ], true);
    }
}
