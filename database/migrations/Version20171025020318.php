<?php

namespace Database\Migrations;

use App\API\V1\Entities\Role;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema as Schema;
use TempestTools\Common\Doctrine\Utility\MakeEmTrait;

class Version20171025020318 extends AbstractMigration
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
            'super-admin'=>[
                '/contexts/super-admin/permissions/{permission}',
                '/contexts/super-admin/roles/{role}',
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
            'super-admin'=>[
                '/contexts/super-admin/permissions/{permission}',
                '/contexts/super-admin/roles/{role}',
            ],
        ], true);
    }
}
