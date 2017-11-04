<?php

use App\API\V1\Entities\Album;
use App\API\V1\Entities\Artist;
use App\API\V1\Entities\User;
use App\API\V1\Repositories\AlbumRepository;
use App\API\V1\Repositories\ArtistRepository;
use App\API\V1\Repositories\UserRepository;
use TempestTools\Common\Constants\CommonArrayObjectKeyConstants;
use TempestTools\Scribe\Orm\Helper\DataBindHelper;
use TempestTools\Scribe\PHPUnit\CrudTestBaseAbstract;


class CudPrePopulateTest extends CrudTestBaseAbstract
{

    /**
     * A basic test example.
     * @group CudPrePopulate
     * @return void
     * @throws Exception
     */
    public function testAssignByIdPrePopulates():void
    {
        $em = $this->em();
        $conn = $em->getConnection();
        $conn->beginTransaction();
        try {
            $arrayHelper = $this->makeArrayHelper();
            /** @var ArtistRepository $artistRepo */
            $artistRepo = $this->em->getRepository(Artist::class);
            $artistRepo->init($arrayHelper, ['testing'], ['testing']);
            /** @var AlbumRepository $albumRepo */
            $albumRepo = $this->em->getRepository(Album::class);
            $albumRepo->init($arrayHelper, ['testing'], ['testing']);
            /** @var Artist[] $artists */
            $artists = $artistRepo->create([
                [
                    'name'=>'BEETHOVEN',
                ],
            ]);
            $albums = $albumRepo->create([
                [
                    'name'=>'BEETHOVEN: THE COMPLETE PIANO SONATAS',
                    'artist'=>$artists[0]->getId(),
                    'releaseDate'=>new \DateTime('now')
                ]
            ], ['clearPrePopulatedEntitiesOnFlush'=>false]);

            /** @noinspection NullPointerExceptionInspection */
            $array = $artistRepo->getArrayHelper()->getArray();
            $prePopulate = $array[CommonArrayObjectKeyConstants::ORM_KEY_NAME][DataBindHelper::PRE_POPULATED_ENTITIES_KEY];
            $this->assertEquals($prePopulate['App\API\V1\Entities\Artist'][$artists[0]->getId()]->getName(), 'BEETHOVEN');


            $conn->rollBack();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }

    /**
     * @group CudPrePopulate
     * @throws Exception
     */
    public function testTurnOffPrePopulate () {
        $em = $this->em();
        $conn = $em->getConnection();
        $conn->beginTransaction();
        try {
            $arrayHelper = $this->makeArrayHelper();
            /** @var ArtistRepository $artistRepo */
            $artistRepo = $this->em->getRepository(Artist::class);
            $artistRepo->init($arrayHelper, ['testTurnOffPrePopulate'], ['testing']);
            /** @var UserRepository $userRepo */
            $userRepo = $this->em->getRepository(User::class);
            $userRepo->init($arrayHelper, ['testing'], ['testing']);
            /** @var User[] $users */
            $users = $userRepo->create($this->createRobAndBobData());

            $userIds = [];
            /** @var User $user */
            foreach ($users as $user) {
                $userIds[] = $user->getId();
            }

            $optionsOverride = ['clearPrePopulatedEntitiesOnFlush'=>false];
            //Test as super admin level permissions to be able to create everything in one fell swoop
            /** @var Artist[] $result */
            $result = $artistRepo->create($this->createArtistChainData($userIds), $optionsOverride);

            /** @noinspection NullPointerExceptionInspection */
            $array = $artistRepo->getArrayHelper()->getArray();

            $prePopulate = $array[CommonArrayObjectKeyConstants::ORM_KEY_NAME][DataBindHelper::PRE_POPULATED_ENTITIES_KEY];

            $this->assertNull($prePopulate);
            /** @var Artist[] $result2 */
            $artistRepo->update([
                $result[0]->getId() => [
                    'name'=>'The artist formerly known as BEETHOVEN',
                    'albums'=>[
                        'update'=>[
                            $result[0]->getAlbums()[0]->getId() => [
                                'name'=>'Kick Ass Piano Solos!'
                            ]
                        ]
                    ]
                ]
            ], $optionsOverride);

            /** @noinspection NullPointerExceptionInspection */
            $array = $artistRepo->getArrayHelper()->getArray();
            $prePopulate = $array[CommonArrayObjectKeyConstants::ORM_KEY_NAME][DataBindHelper::PRE_POPULATED_ENTITIES_KEY];

            $this->assertNull($prePopulate);



            $artistRepo->delete([
                $result[0]->getId() => [
                    'albums'=>[
                        'delete'=>[
                            $result[0]->getAlbums()[0]->getId() => [
                            ]
                        ]
                    ]
                ]
            ], $optionsOverride);

            $prePopulate = $array[CommonArrayObjectKeyConstants::ORM_KEY_NAME][DataBindHelper::PRE_POPULATED_ENTITIES_KEY];

            $this->assertNull($prePopulate);

            $conn->rollBack();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }

    /**
     * @group CudPrePopulate
     * @throws Exception
     */
    public function testPrePopulate () {
        $em = $this->em();
        $conn = $em->getConnection();
        $conn->beginTransaction();
        try {
            $arrayHelper = $this->makeArrayHelper();
            /** @var ArtistRepository $artistRepo */
            $artistRepo = $this->em->getRepository(Artist::class);
            $artistRepo->init($arrayHelper, ['admin'], ['testing']);
            /** @var UserRepository $userRepo */
            $userRepo = $this->em->getRepository(User::class);
            $userRepo->init($arrayHelper, ['testing'], ['testing']);
            /** @var User[] $users */
            $users = $userRepo->create($this->createRobAndBobData());

            $userIds = [];
            /** @var User $user */
            foreach ($users as $user) {
                $userIds[] = $user->getId();
            }

            $optionsOverride = ['clearPrePopulatedEntitiesOnFlush'=>false];
            //Test as super admin level permissions to be able to create everything in one fell swoop
            /** @var Artist[] $result */
            $result = $artistRepo->create($this->createArtistChainData($userIds), $optionsOverride);

            $user = $result[0]->getAlbums()[0]->getUsers()[0];

            $this->assertTrue($user->isPrePopulated());
            /** @noinspection NullPointerExceptionInspection */
            $array = $artistRepo->getArrayHelper()->getArray();

            $prePopulate = $array[CommonArrayObjectKeyConstants::ORM_KEY_NAME][DataBindHelper::PRE_POPULATED_ENTITIES_KEY];

            $this->assertEquals($prePopulate['App\API\V1\Entities\User'][$userIds[0]]->getName(), 'bob');
            $this->assertEquals($prePopulate['App\API\V1\Entities\User'][$userIds[1]]->getName(), 'rob');
            /** @var Artist[] $result2 */
            $result2 = $artistRepo->update([
                $result[0]->getId() => [
                    'name'=>'The artist formerly known as BEETHOVEN',
                    'albums'=>[
                        'update'=>[
                            $result[0]->getAlbums()[0]->getId() => [
                                'name'=>'Kick Ass Piano Solos!'
                            ]
                        ]
                    ]
                ]
            ], $optionsOverride);

            /** @noinspection NullPointerExceptionInspection */
            $array = $artistRepo->getArrayHelper()->getArray();
            $prePopulate = $array[CommonArrayObjectKeyConstants::ORM_KEY_NAME][DataBindHelper::PRE_POPULATED_ENTITIES_KEY];

            $this->assertEquals($prePopulate['App\API\V1\Entities\Artist'][$result2[0]->getId()]->getName(), 'The artist formerly known as BEETHOVEN');
            $this->assertEquals($prePopulate['App\API\V1\Entities\Album'][$result2[0]->getAlbums()[0]->getId()]->getName(), 'Kick Ass Piano Solos!');

            $artistRepo->update([
                $result[0]->getId() => [
                    'name'=>'The artist formerly known as BEETHOVEN',
                    'albums'=>[
                        'update'=>[
                            $result[0]->getAlbums()[0]->getId() => [
                                'name'=>'Kick Ass Piano Solos!'
                            ]
                        ]
                    ]
                ]
            ]);

            $prePopulate = $array[CommonArrayObjectKeyConstants::ORM_KEY_NAME][DataBindHelper::PRE_POPULATED_ENTITIES_KEY];

            $this->assertNull($prePopulate['App\API\V1\Entities\Artist']);

            $artistId = $result[0]->getId();
            $albumId = $result[0]->getAlbums()[0]->getId();
            $artistRepo->delete([
                $result[0]->getId() => [
                    'albums'=>[
                        'delete'=>[
                            $result[0]->getAlbums()[0]->getId() => [
                            ]
                        ]
                    ]
                ]
            ], $optionsOverride);

            $prePopulate = $array[CommonArrayObjectKeyConstants::ORM_KEY_NAME][DataBindHelper::PRE_POPULATED_ENTITIES_KEY];

            $this->assertEquals($prePopulate['App\API\V1\Entities\Artist'][$artistId]->getName(), 'The artist formerly known as BEETHOVEN');
            $this->assertEquals($prePopulate['App\API\V1\Entities\Album'][$albumId]->getName(), 'Kick Ass Piano Solos!');

            $conn->rollBack();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }


}
