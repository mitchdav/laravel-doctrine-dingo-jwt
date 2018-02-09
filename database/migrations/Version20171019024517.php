<?php

namespace Database\Migrations;

use App\API\V1\Entities\Permission;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema as Schema;
use TempestTools\Common\Doctrine\Utility\MakeEmTrait;

class Version20171019024517 extends AbstractMigration
{
    use MakeEmTrait;
    /**
     * @param Schema $schema
     */
    /**
     * @param Schema $schema
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Exception
     */
    public function up(Schema $schema):void
    {
        $conn = $this->em()->getConnection();
        $conn->beginTransaction();
        try {
            $permissionRepo =  $this->em()->getRepository(Permission::class);
            $perm = $permissionRepo->findOneBy(['name'=>'album']);
            $perm->setName('/contexts/user/albums');
            $this->em()->persist($perm);

            $perm = $permissionRepo->findOneBy(['name'=>'album/{album}']);
            $perm->setName('/contexts/user/albums/{album}');
            $this->em()->persist($perm);

            $perm = $permissionRepo->findOneBy(['name'=>'artist']);
            $perm->setName('/contexts/user/artists');
            $this->em()->persist($perm);

            $perm = $permissionRepo->findOneBy(['name'=>'artist/{artist}']);
            $perm->setName('/contexts/user/artists/{artist}');
            $this->em()->persist($perm);

            $perm = $permissionRepo->findOneBy(['name'=>'user']);
            $perm->setName('/contexts/user/users');
            $this->em()->persist($perm);

            $perm = $permissionRepo->findOneBy(['name'=>'user/{user}']);
            $perm->setName('/contexts/user/users/{user}');
            $this->em()->persist($perm);


            $this->em()->flush();
            $conn->commit();
        } catch (\Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }

    /**
     * @param Schema $schema
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Exception
     */
    public function down(Schema $schema):void
    {
        $conn = $this->em()->getConnection();
        $conn->beginTransaction();
        try {
            $permissionRepo =  $this->em()->getRepository(Permission::class);
            $perm = $permissionRepo->findOneBy(['name'=>'/contexts/user/albums']);
            $perm->setName('album');
            $this->em()->persist($perm);

            $perm = $permissionRepo->findOneBy(['name'=>'/contexts/user/albums/{album}']);
            $perm->setName('album/{album}');
            $this->em()->persist($perm);

            $perm = $permissionRepo->findOneBy(['name'=>'/contexts/user/artists']);
            $perm->setName('artist');
            $this->em()->persist($perm);

            $perm = $permissionRepo->findOneBy(['name'=>'/contexts/user/artists/{artist}']);
            $perm->setName('artist/{artist}');
            $this->em()->persist($perm);

            $perm = $permissionRepo->findOneBy(['name'=>'/contexts/user/users']);
            $perm->setName('user');
            $this->em()->persist($perm);

            $perm = $permissionRepo->findOneBy(['name'=>'/contexts/user/users/{user}']);
            $perm->setName('user/{user}');
            $this->em()->persist($perm);

            $this->em()->flush();
            $conn->commit();
        } catch (\Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }
}
