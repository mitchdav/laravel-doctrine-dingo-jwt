<?php

namespace Database\Migrations;

use App\API\V1\Entities\Role;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema as Schema;
use TempestTools\Common\Doctrine\Utility\MakeEmTrait;

class Version20171018030733 extends AbstractMigration
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
                '/contexts/user/users/{user}/albums',
            ],
            'admin'=>[
                '/contexts/admin/users/{user}/albums',
            ],
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
                '/contexts/user/users/{user}/albums',
            ],
            'admin'=>[
                '/contexts/admin/users/{user}/albums',
            ],
        ], true);
    }
}
