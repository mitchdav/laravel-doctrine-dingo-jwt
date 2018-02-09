<?php

namespace Database\Migrations;

use App\API\V1\Entities\Permission;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema as Schema;
use TempestTools\Common\Doctrine\Utility\MakeEmTrait;

class Version20171004023833 extends AbstractMigration
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
            $perm = $permissionRepo->findOneBy(['name'=>'admin/album']);
            $perm->setName('/admin/album');
            $this->em()->persist($perm);

            $perm = $permissionRepo->findOneBy(['name'=>'admin/album/{album}']);
            $perm->setName('/admin/album/{album}');
            $this->em()->persist($perm);

            $perm = $permissionRepo->findOneBy(['name'=>'admin/artist']);
            $perm->setName('/admin/artist');
            $this->em()->persist($perm);

            $perm = $permissionRepo->findOneBy(['name'=>'admin/artist/{artist}']);
            $perm->setName('/admin/artist/{artist}');
            $this->em()->persist($perm);

            $perm = $permissionRepo->findOneBy(['name'=>'admin/user']);
            $perm->setName('/admin/user');
            $this->em()->persist($perm);

            $perm = $permissionRepo->findOneBy(['name'=>'admin/user/{user}']);
            $perm->setName('/admin/user/{user}');
            $this->em()->persist($perm);

            $perm = $permissionRepo->findOneBy(['name'=>'super-admin/user']);
            $perm->setName('/super-admin/user');
            $this->em()->persist($perm);

            $perm = $permissionRepo->findOneBy(['name'=>'super-admin/user/{user}']);
            $perm->setName('/super-admin/user/{user}');
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
            $perm = $permissionRepo->findOneBy(['name'=>'/admin/album']);
            $perm->setName('admin/album');
            $this->em()->persist($perm);

            $perm = $permissionRepo->findOneBy(['name'=>'/admin/album/{album}']);
            $perm->setName('admin/album/{album}');
            $this->em()->persist($perm);

            $perm = $permissionRepo->findOneBy(['name'=>'/admin/artist']);
            $perm->setName('admin/artist');
            $this->em()->persist($perm);

            $perm = $permissionRepo->findOneBy(['name'=>'/admin/artist/{artist}']);
            $perm->setName('admin/artist/{artist}');
            $this->em()->persist($perm);

            $perm = $permissionRepo->findOneBy(['name'=>'/admin/user']);
            $perm->setName('admin/user');
            $this->em()->persist($perm);

            $perm = $permissionRepo->findOneBy(['name'=>'/admin/user/{user}']);
            $perm->setName('admin/user/{user}');
            $this->em()->persist($perm);

            $perm = $permissionRepo->findOneBy(['name'=>'/super-admin/user']);
            $perm->setName('super-admin/user');
            $this->em()->persist($perm);

            $perm = $permissionRepo->findOneBy(['name'=>'/super-admin/user/{user}']);
            $perm->setName('super-admin/user/{user}');
            $this->em()->persist($perm);

            $this->em()->flush();
            $conn->commit();
        } catch (\Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }
}
