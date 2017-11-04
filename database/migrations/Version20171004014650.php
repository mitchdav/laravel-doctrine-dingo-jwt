<?php

namespace Database\Migrations;

use App\API\V1\Entities\Album;
use App\API\V1\Entities\Artist;
use App\API\V1\Entities\User;
use DateTime;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema as Schema;
use TempestTools\Common\Doctrine\Utility\MakeEmTrait;

class Version20171004014650 extends AbstractMigration
{
    use MakeEmTrait;

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
            $userRepo =  $this->em()->getRepository(User::class);
            $user = $userRepo->findOneBy(['id'=>1]);
            $artist = new Artist();
            $artist->setName('Brahms');

            $album = new Album();
            $album->setName('Brahms: Complete Edition');
            $album->setReleaseDate(new DateTime('1995-02-01 00:00:00'));
            $album->addUser($user);

            $artist->addAlbum($album);
            $this->em()->persist($artist);
            $this->em()->persist($album);
            $this->em()->persist($user);
            $this->em()->flush();
            $conn->commit();
        } catch (\Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }

    /**
     * @param Schema $schema
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Exception
     */
    public function down(Schema $schema):void
    {
        $conn = $this->em()->getConnection();
        $conn->beginTransaction();
        try {
            $userRepo =  $this->em()->getRepository(User::class);
            $user = $userRepo->findOneBy(['id'=>1]);
            $artistRepo =  $this->em()->getRepository(Artist::class);
            $artist = $artistRepo->findOneBy(['name'=>'Brahms']);
            $albumRepo =  $this->em()->getRepository(Album::class);
            $album = $albumRepo->findOneBy(['name'=>'Brahms: Complete Edition']);
            $user->removeAlbum($album);
            $this->em()->persist($user);
            $this->em()->remove($artist);
            $this->em()->remove($album);
            $this->em()->flush();
            $conn->commit();
        } catch (\Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }
}
