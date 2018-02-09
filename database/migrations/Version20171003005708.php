<?php

namespace Database\Migrations;

use App\API\V1\Entities\Permission;
use App\API\V1\Entities\Role;
use App\API\V1\Entities\User;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema as Schema;
use TempestTools\Common\Doctrine\Utility\MakeEmTrait;
use Faker\Factory;

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
        $generator = Factory::create();

        $user = new User();
        $user
            ->setEmail(env('BASE_USER_EMAIL'))
            ->setPassword(env('BASE_USER_EMAIL'))
            ->setName(env('BASE_USER_NAME'))
            ->setJob($generator->jobTitle)
            ->setAddress($generator->address);

        $this->em->persist($user);

        $this->em->flush();

        $authenticatePerm = new Permission();
        $authenticatePerm->setName('auth/authenticate:POST');
        $refreshPerm = new Permission();
        $refreshPerm->setName('auth/refresh:GET');
        $mePerm = new Permission();
        $mePerm->setName('auth/me:GET');

        $userRole = new Role();
        $userRole->setName('user');
        $userRole->addPermission($refreshPerm);
        $userRole->addPermission($mePerm);
        //$userRole->setPermissions(new ArrayCollection([$refreshPerm, $mePerm]));
        $adminRole = new Role();
        $adminRole->setName('admin');
        $superAdminRole = new Role();
        $superAdminRole->setName('super-admin');

        /** @var User $baseUser */
        $baseUser = $this->em->getRepository(User::class)->find(1);
        $baseUser->addRole($userRole);
        $baseUser->addRole($adminRole);
        $baseUser->addRole($superAdminRole);

        $this->em->persist($authenticatePerm);
        $this->em->persist($refreshPerm);
        $this->em->persist($mePerm);

        $this->em->persist($userRole);
        $this->em->persist($adminRole);
        $this->em->persist($superAdminRole);

        $this->em->persist($baseUser);

        $this->em->flush();

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
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
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

        $baseUser = $this->em->getRepository(User::class)->find(1);
        $em->remove($baseUser);
        $authenticatePerm = $this->em->getRepository(Permission::class)->find('auth/authenticate:POST');
        $em->remove($authenticatePerm);
        $refreshPerm = $this->em->getRepository(Permission::class)->find('auth/refresh:GET');
        $em->remove($refreshPerm);
        $mePerm = $this->em->getRepository(Permission::class)->find('auth/me:GET');
        $em->remove($mePerm);

        $userRole = $this->em->getRepository(Role::class)->findBy(['name'=>'user']);
        $em->remove($userRole);
        $adminRole = $this->em->getRepository(Role::class)->findBy(['name'=>'admin']);
        $em->remove($adminRole);
        $superAdminRole = $this->em->getRepository(Role::class)->findBy(['name'=>'super-admin']);
        $em->remove($superAdminRole);
    }
}
