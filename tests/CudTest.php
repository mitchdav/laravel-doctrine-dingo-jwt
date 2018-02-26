<?php

use App\API\V1\Entities\Album;
use App\API\V1\Entities\Artist;
use App\API\V1\Entities\User;
use App\API\V1\Repositories\AlbumRepository;
use App\API\V1\Repositories\ArtistRepository;
use App\API\V1\Repositories\UserRepository;
use TempestTools\Scribe\Constants\EntityEventsConstants;
use TempestTools\Scribe\Constants\RepositoryEventsConstants;
use TempestTools\Scribe\Exceptions\Orm\EntityException;
use TempestTools\Scribe\Exceptions\Orm\Helper\DataBindHelperException;
use TempestTools\Scribe\Exceptions\Orm\Helper\EntityArrayHelperException;
use TempestTools\Scribe\PHPUnit\CrudTestBaseAbstract;


class CudTest extends CrudTestBaseAbstract
{

    /**
     * @group CrudCudOnly
     * @throws Exception
     */
    public function testSimpleParamSyntax ():void
    {
        $em = $this->em();
        $conn = $em->getConnection();
        $conn->beginTransaction();
        try {
            $arrayHelper = $this->makeArrayHelper();

            /** @var UserRepository $userRepo */
            $userRepo = $this->em->getRepository(User::class);
            $userRepo->init($arrayHelper, ['testing'], ['testing']);
            /** @var User[] $users */
            $users = $userRepo->create($this->createRobAndBobData());

            /** @var AlbumRepository $albumRepo */
            $albumRepo = $this->em->getRepository(Album::class);
            $albumRepo->init($arrayHelper, ['testing'], ['testing']);
            /** @var Album[] $result */
            $result = $albumRepo->create([ // Test top level create
                [
                    'name'=>'BEETHOVEN: THE COMPLETE PIANO SONATAS',
                    'releaseDate'=>new \DateTime('now'),
                    'artist'=> [ // test nested create in single relation
                        'name'=>'BEETHOVEN',
                    ],
                    'users'=>[ //test nested read in many relation
                        [
                            'id'=>$users[0]->getId()
                        ]
                    ],
                ],
            ], [], ['simplifiedParams'=>true]);

            $this->assertEquals($result[0]->getName(), 'BEETHOVEN: THE COMPLETE PIANO SONATAS');
            /** @noinspection NullPointerExceptionInspection */
            $this->assertEquals($result[0]->getArtist()->getName(), 'BEETHOVEN');
            $this->assertEquals($result[0]->getUsers()[0]->getId(), $users[0]->getId());
            /** @noinspection NullPointerExceptionInspection */
            $artistId = $result[0]->getArtist()->getId();
            /** @var Album[] $result */
            /** @noinspection NullPointerExceptionInspection */
            $result = $albumRepo->update([ // Test top level update
                [
                    'id'=>$result[0]->getId(),
                    'name'=>'UPDATED',
                    'artist'=> [ // test nested updated in single relation
                        'id'=>$result[0]->getArtist()->getId(),
                        'name'=>'UPDATED',
                    ],
                    'users'=>[ //test nested update in many relation
                        [
                            'id'=>$users[0]->getId(),
                            'name'=>'UPDATED',
                            'assignType'=>'null'
                        ]
                    ],
                ],
            ], [], ['simplifiedParams'=>true]);

            $this->assertEquals($result[0]->getName(), 'UPDATED');
            /** @noinspection NullPointerExceptionInspection */
            $this->assertEquals($result[0]->getArtist()->getName(), 'UPDATED');
            $this->assertEquals($result[0]->getUsers()[0]->getName(), 'UPDATED');



            /** @var Album[] $resultRemove */
            $resultRemove = $albumRepo->update([ // Test top level update
                [
                    'id'=>$result[0]->getId(),
                    'artist'=> null,
                    'users'=>[ //test nested update in many relation
                        [
                            'id'=>$users[0]->getId(),
                            'name'=>'REMOVED',
                            'assignType'=>'removeSingle'
                        ]
                    ],
                ],
            ], [], ['simplifiedParams'=>true]);

            $users2 = $resultRemove[0]->getUsers();
            $this->assertCount(0, $users2);
            $this->assertEquals($resultRemove[0]->getArtist(), null);

            // Set it back for more testing
            /** @var Album[] $result */
            $result = $albumRepo->update([ // Test top level update
                [
                    'id'=>$result[0]->getId(),
                    'name'=>'UPDATED',
                    'artist'=> [ // test nested updated in single relation
                        'id'=>$artistId,
                        'name'=>'UPDATED',
                    ],
                    'users'=>[ //test nested update in many relation
                        [
                            'id'=>$users[0]->getId(),
                            'name'=>'UPDATED',
                            'assignType'=>'null'
                        ]
                    ],
                ],
            ], [], ['simplifiedParams'=>true]);

            /** @var Album[] $result */
            /** @noinspection NullPointerExceptionInspection */
            $result = $albumRepo->delete([ // Test top level update
                [
                    'id'=>$result[0]->getId(),
                    'artist'=> [ // test nested updated in single relation
                        'id'=>$result[0]->getArtist()->getId(),
                        'chainType'=>'delete',
                    ],
                    'users'=>[ //test nested update in many relation
                        [
                            'id'=>$users[0]->getId(),
                            'chainType'=>'delete'
                        ]
                    ],
                ],
            ], [], ['simplifiedParams'=>true]);

            $users3 = $result[0]->getUsers();
            /** @noinspection NullPointerExceptionInspection */
            $this->assertEquals($result[0]->getArtist()->getId(), null);
            $this->assertEquals($result[0]->getId(), null);
            $this->assertEquals($users3[0]->getId(), null);

            $conn->rollBack();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }

    /**
     * A basic test example.
     * @group CrudCudOnly
     * @return void
     * @throws Exception
     */
    public function testCantAssignNonField():void
    {
        $em = $this->em();
        $conn = $em->getConnection();
        $conn->beginTransaction();
        try {
            $arrayHelper = $this->makeArrayHelper();

            /** @var AlbumRepository $albumRepo */
            $albumRepo = $this->em->getRepository(Album::class);
            $albumRepo->init($arrayHelper, ['testing'], ['testing']);
            $e = null;
            try {
                $albumRepo->create([
                    [
                        'notAField' => 'setting a non field!',
                    ]
                ]);
            } catch (Exception $e) {

            }
            $this->assertEquals(get_class($e), DataBindHelperException::class);
            $this->assertEquals($e->getMessage(), 'Error: You attempted to access a property of an entity that wasn\'t a field. entity name = App\API\V1\Entities\Album, property name = notAField');
            $conn->rollBack();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }



    /**
     * @group CrudCudOnly
     * @throws Exception
     */
    public function testNullAssignType () {
        $em = $this->em();
        $conn = $em->getConnection();
        $conn->beginTransaction();
        try {
            $arrayHelper = $this->makeArrayHelper();
            /** @var ArtistRepository $artistRepo */
            $artistRepo = $this->em->getRepository(Artist::class);
            $artistRepo->init($arrayHelper, ['testNullAssignType'], ['testing']);
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

            //Test as super admin level permissions to be able to create everything in one fell swoop
            /** @var Artist[] $result */
            $result = $artistRepo->create($this->createArtistChainData($userIds));
            $e = null;
            try {
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
                ]);
            } catch (Exception $e){

            }

            $this->assertEquals(get_class($e), EntityArrayHelperException::class);
            $conn->rollBack();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }

    /**
     * @group CrudCudOnly
     * @throws Exception
     */
    public function testUpdateWithChainAndEvents () {
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

            //Test as super admin level permissions to be able to create everything in one fell swoop
            /** @var Artist[] $result */
            $result = $artistRepo->create($this->createArtistChainData($userIds));
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
            ]);

            $this->assertEquals($result2[0]->getName(), 'The artist formerly known as BEETHOVEN');
            $this->assertEquals($result2[0]->getAlbums()[0]->getName(), 'Kick Ass Piano Solos!');

            /** @noinspection NullPointerExceptionInspection */
            $array = $artistRepo->getArrayHelper()->getArray()->getArrayCopy();

            foreach ([
                 RepositoryEventsConstants::PRE_START,
                 RepositoryEventsConstants::PRE_STOP,
                 RepositoryEventsConstants::PRE_UPDATE_BATCH,
                 RepositoryEventsConstants::PRE_UPDATE,
                 RepositoryEventsConstants::VALIDATE_UPDATE,
                 RepositoryEventsConstants::VERIFY_UPDATE,
                 RepositoryEventsConstants::PROCESS_RESULTS_UPDATE,
                 RepositoryEventsConstants::POST_UPDATE,
                 RepositoryEventsConstants::POST_UPDATE_BATCH
             ] as $event) {
                $this->assertArrayHasKey($event, $array['repoEvents']);
            }

            $conn->rollBack();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }

    /**
     * @group CrudCudOnly
     * @throws Exception
     */
    public function testMultiDeleteAndEvents () {
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

            //Test as super admin level permissions to be able to create everything in one fell swoop
            /** @var Artist[] $result */
            $result = $artistRepo->create($this->createArtistChainData($userIds));
            /** @var Artist[] $result2 */
            $result2 = $artistRepo->delete([
                $result[0]->getId() => [

                ],
                $result[1]->getId() => [

                ]
            ]);

            $this->assertNull($result2[0]->getId());
            $this->assertNull($result2[1]->getId());

            /** @noinspection NullPointerExceptionInspection */
            $array = $artistRepo->getArrayHelper()->getArray()->getArrayCopy();

            foreach ([
                 RepositoryEventsConstants::PRE_START,
                 RepositoryEventsConstants::PRE_STOP,
                 RepositoryEventsConstants::PRE_DELETE_BATCH,
                 RepositoryEventsConstants::PRE_DELETE,
                 RepositoryEventsConstants::VALIDATE_DELETE,
                 RepositoryEventsConstants::VERIFY_DELETE,
                 RepositoryEventsConstants::PROCESS_RESULTS_DELETE,
                 RepositoryEventsConstants::POST_DELETE,
                 RepositoryEventsConstants::POST_DELETE_BATCH
             ] as $event) {
                $this->assertArrayHasKey($event, $array['repoEvents']);
            }


            $conn->rollBack();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }


    /**
     * @group CrudCudOnly
     * @throws Exception
     */
    public function testChainRemove () {
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

            //Test as super admin level permissions to be able to create everything in one fell swoop
            /** @var Artist[] $result */
            $result = $artistRepo->create($this->createArtistChainData($userIds));
            $artistRepo->update([
                $result[0]->getId() => [
                    'albums'=>[
                        'update'=>[
                            $result[0]->getAlbums()[0]->getId() => [
                                'assignType'=>'removeSingle',
                            ],
                            $result[0]->getAlbums()[1]->getId() => [
                                'assignType'=>'removeSingle',
                            ]
                        ]
                    ]
                ]
            ]);

            $this->assertCount(0, $result[0]->getAlbums());

            $conn->rollBack();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }


    /**
     * @group CrudCudOnly
     * @throws Exception
     */
    public function testChainDelete () {
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

            //Test as super admin level permissions to be able to create everything in one fell swoop
            /** @var Artist[] $result */
            $result = $artistRepo->create($this->createArtistChainData($userIds));
            $artistRepo->update([
                $result[0]->getId() => [
                    'albums'=>[
                        'delete'=>[
                            $result[0]->getAlbums()[0]->getId() => [
                                'assignType'=>'removeSingle',
                            ],
                            $result[0]->getAlbums()[1]->getId() => [
                                'assignType'=>'removeSingle',
                            ]
                        ]
                    ]
                ]
            ]);

            $this->assertCount(0, $result[0]->getAlbums());

            $conn->rollBack();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }


    /**
     * A basic test example.
     * @group CrudCudOnly
     * @return void
     * @throws Exception
     */
    public function testNoFlush():void
    {
        $em = $this->em();
        $conn = $em->getConnection();
        $conn->beginTransaction();
        try {
            /** @var AlbumRepository $albumRepo */
            $albumRepo = $this->em->getRepository(Album::class);
            $arrayHelper = $this->makeArrayHelper();
            //Test as super admin level permissions to be able to create everything in one fell swoop
            $albumRepo->init($arrayHelper, ['testTopLevelSetToAndMutate'], ['testing']);
            /** @var Album[] $result */
            $result = $albumRepo->create($this->createData(), ['flush'=>false]);

            $this->assertEquals($result[0]->getId(), NULL);

            $em->flush();
            $conn->rollBack();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }


    /**
     * A basic test example.
     * @group CrudCudOnly
     * @return void
     * @throws Exception
     */
    public function testEventsFire():void
    {
        $em = $this->em();
        $conn = $em->getConnection();
        $conn->beginTransaction();
        try {
            /** @var AlbumRepository $albumRepo */
            $albumRepo = $this->em->getRepository(Album::class);
            $arrayHelper = $this->makeArrayHelper();
            //Test as super admin level permissions to be able to create everything in one fell swoop
            $albumRepo->init($arrayHelper, ['testTopLevelSetToAndMutate'], ['testing']);
            $albumRepo->create($this->createData());

            /** @noinspection NullPointerExceptionInspection */
            $array = $albumRepo->getArrayHelper()->getArray()->getArrayCopy();

            $this->assertArrayHasKey('params', $array['repoEvents']['preStart']);
            $this->assertArrayHasKey('arrayHelper', $array['repoEvents']['preStart']);
            $this->assertArrayHasKey('results', $array['repoEvents']['preStart']);
            $this->assertArrayHasKey('self', $array['repoEvents']['preStart']);
            $this->assertArrayHasKey('optionOverrides', $array['repoEvents']['preStart']);
            $this->assertArrayHasKey('entitiesShareConfigs', $array['repoEvents']['preStart']);
            $this->assertArrayHasKey('frontEndOptions', $array['repoEvents']['preStart']);
            $this->assertArrayHasKey('action', $array['repoEvents']['preStart']);

            foreach ([
                 RepositoryEventsConstants::PRE_START,
                 RepositoryEventsConstants::PRE_STOP,
                 RepositoryEventsConstants::PRE_CREATE_BATCH,
                 RepositoryEventsConstants::PRE_CREATE,
                 RepositoryEventsConstants::VALIDATE_CREATE,
                 RepositoryEventsConstants::VERIFY_CREATE,
                 RepositoryEventsConstants::PROCESS_RESULTS_CREATE,
                 RepositoryEventsConstants::POST_CREATE,
                 RepositoryEventsConstants::POST_CREATE_BATCH
             ] as $event) {
                $this->assertArrayHasKey($event, $array['repoEvents']);
            }

            foreach ([
                 EntityEventsConstants::PRE_SET_FIELD,
                 EntityEventsConstants::PRE_PROCESS_ASSOCIATION_PARAMS,
                 EntityEventsConstants::PRE_PERSIST,
                 EntityEventsConstants::POST_PERSIST,
             ] as $event) {
                $this->assertArrayHasKey($event, $array['entityEvents']);
            }

            $em->flush();
            $conn->rollBack();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }

    /**
     * A basic test example.
     * @group CrudCudOnly
     * @return void
     * @throws Exception
     */
    public function testMaxBatch():void
    {
        $em = $this->em();
        $conn = $em->getConnection();
        $conn->beginTransaction();
        try {
            $arrayHelper = $this->makeArrayHelper();
            /** @var ArtistRepository $artistRepo */
            $artistRepo = $this->em->getRepository(Artist::class);
            $artistRepo->init($arrayHelper, ['testing'], ['testing']);

            $e = NULL;
            try {
                $artistRepo->create([
                    [
                        'name'=>'BEETHOVEN',
                    ],
                    [
                        'name'=>'BACH',
                    ]
                ], ['batchMax'=>1]);
            } catch (Exception $e) {

            }
            $this->assertEquals(get_class($e), DataBindHelperException::class);

            $conn->rollBack();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }

    /**
     * A basic test example.
     * @group CrudCudOnly
     * @return void
     * @throws Exception
     */
    public function testAssignById():void
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
            ]);
            /** @var Album[] $albums */
            /** @var Artist $artist */
            $artist = $albums[0]->getArtist();
            $this->assertEquals($artist->getId(), $artists[0]->getId());

            $conn->rollBack();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }

    /**
     * A basic test example.
     * @group CrudCudOnly
     * @return void
     * @throws Exception
     */
    public function testMultiAddAndChain():void
    {
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


            //Test as super admin level permissions to be able to create everything in one fell swoop
            /** @var Artist[] $result */
            $result = $artistRepo->create($this->createArtistChainData($userIds));
            $this->assertEquals($result[0]->getName(), 'BEETHOVEN');
            $this->assertEquals($result[0]->getAlbums()[0]->getName(), 'BEETHOVEN: THE COMPLETE PIANO SONATAS');
            $this->assertEquals($result[0]->getAlbums()[1]->getName(), 'BEETHOVEN: THE COMPLETE STRING QUARTETS');
            $user = $result[0]->getAlbums()[0]->getUsers()[0];
            $this->assertEquals($user->getName(), 'bob');
            $user = $result[0]->getAlbums()[0]->getUsers()[1];
            $this->assertEquals($user->getName(), 'rob');
            $user = $result[0]->getAlbums()[1]->getUsers()[0];
            $this->assertEquals($user->getName(), 'bob');
            $user = $result[0]->getAlbums()[1]->getUsers()[1];
            $this->assertEquals($user->getName(), 'rob');
            $this->assertEquals($result[1]->getName(), 'BACH');
            $this->assertEquals($result[1]->getAlbums()[0]->getName(), 'Amsterdam Baroque Orchestra');
            $this->assertEquals($result[1]->getAlbums()[1]->getName(), 'The English Suites');
            $user = $result[1]->getAlbums()[0]->getUsers()[0];
            $this->assertEquals($user->getName(), 'bob');
            $user = $result[1]->getAlbums()[0]->getUsers()[1];
            $this->assertEquals($user->getName(), 'rob');
            $user = $result[1]->getAlbums()[1]->getUsers()[0];
            $this->assertEquals($user->getName(), 'bob');
            $user = $result[1]->getAlbums()[1]->getUsers()[1];
            $this->assertEquals($user->getName(), 'rob');

            $conn->rollBack();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }


    /**
     * A basic test example.
     * @group CrudCudOnly
     * @return void
     * @throws Exception
     */
    public function testLowLevelMutate():void
    {
        $em = $this->em();
        $conn = $em->getConnection();
        $conn->beginTransaction();
        try {
            /** @var AlbumRepository $albumRepo */
            $albumRepo = $this->em->getRepository(Album::class);
            $arrayHelper = $this->makeArrayHelper();
            //Test as super admin level permissions to be able to create everything in one fell swoop
            $albumRepo->init($arrayHelper, ['testLowLevelMutate'], ['testing']);
            $e = NULL;
            /** @var Album[] $result */
            $result = $albumRepo->create($this->createData());
            $album = $result[0];
            $this->assertEquals($album->getName(), 'foobar');

            $conn->rollBack();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }

    /**
     * A basic test example.
     * @group CrudCudOnly
     * @return void
     * @throws Exception
     */
    public function testLowLevelClosure():void
    {
        $em = $this->em();
        $conn = $em->getConnection();
        $conn->beginTransaction();
        try {
            /** @var AlbumRepository $albumRepo */
            $albumRepo = $this->em->getRepository(Album::class);
            $arrayHelper = $this->makeArrayHelper();
            //Test as super admin level permissions to be able to create everything in one fell swoop
            $albumRepo->init($arrayHelper, ['testLowLevelClosure'], ['testing']);
            $e = NULL;
            /** @var Album[] $result */
            try {
                $createData = $this->createData();
                $albumRepo->create($createData);
            } catch (Exception $e) {

            }
            $this->assertEquals(get_class($e), EntityArrayHelperException::class);

            $conn->rollBack();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }


    /**
     * A basic test example.
     * @group CrudCudOnly
     * @return void
     * @throws Exception
     */
    public function testLowLevelEnforceOnRelation():void
    {
        $em = $this->em();
        $conn = $em->getConnection();
        $conn->beginTransaction();
        try {
            /** @var AlbumRepository $albumRepo */
            $albumRepo = $this->em->getRepository(Album::class);
            $arrayHelper = $this->makeArrayHelper();
            //Test as super admin level permissions to be able to create everything in one fell swoop
            $albumRepo->init($arrayHelper, ['testLowLevelEnforceOnRelation'], ['testing']);
            $e = NULL;
            /** @var Album[] $result */
            try {
                $createData = $this->createData();
                $albumRepo->create($createData);
            } catch (Exception $e) {

            }
            $this->assertEquals(get_class($e), EntityArrayHelperException::class);

            $conn->rollBack();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }


    /**
     * A basic test example.
     * @group CrudCudOnly
     * @return void
     * @throws Exception
     */
    public function testLowLevelEnforce():void
    {
        $em = $this->em();
        $conn = $em->getConnection();
        $conn->beginTransaction();
        try {
            /** @var AlbumRepository $albumRepo */
            $albumRepo = $this->em->getRepository(Album::class);
            $arrayHelper = $this->makeArrayHelper();
            //Test as super admin level permissions to be able to create everything in one fell swoop
            $albumRepo->init($arrayHelper, ['testLowLevelEnforce'], ['testing']);
            $e = NULL;
            /** @var Album[] $result */
            try {
                $createData = $this->createData();
                $albumRepo->create($createData);
            } catch (Exception $e) {

            }
            $this->assertEquals(get_class($e), EntityArrayHelperException::class);

            $conn->rollBack();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }


    /**
     * A basic test example.
     * @group CrudCudOnly
     * @return void
     * @throws Exception
     */
    public function testTopLevelClosure():void
    {
        $em = $this->em();
        $conn = $em->getConnection();
        $conn->beginTransaction();
        try {
            /** @var AlbumRepository $albumRepo */
            $albumRepo = $this->em->getRepository(Album::class);
            $arrayHelper = $this->makeArrayHelper();
            //Test as super admin level permissions to be able to create everything in one fell swoop
            $albumRepo->init($arrayHelper, ['testTopLevelClosure'], ['testing']);
            $e = NULL;
            /** @var Album[] $result */
            try {
                $createData = $this->createData();
                $albumRepo->create($createData);
            } catch (Exception $e) {

            }
            $this->assertEquals(get_class($e), EntityArrayHelperException::class);

            $conn->rollBack();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }


    /**
     * A basic test example.
     * @group CrudCudOnly
     * @return void
     * @throws Exception
     */
    public function testEnforceTopLevelWorks():void
    {
        $em = $this->em();
        $conn = $em->getConnection();
        $conn->beginTransaction();
        try {
            /** @var AlbumRepository $albumRepo */
            $albumRepo = $this->em->getRepository(Album::class);
            $arrayHelper = $this->makeArrayHelper();
            //Test as super admin level permissions to be able to create everything in one fell swoop
            $albumRepo->init($arrayHelper, ['testEnforceTopLevelWorks'], ['testing']);
            $e = NULL;
            /** @var Album[] $result */
            try {
                $createData = $this->createData();
                $albumRepo->create($createData);
            } catch (Exception $e) {

            }
            $this->assertEquals(get_class($e), EntityArrayHelperException::class);

            $conn->rollBack();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }



    /**
     * A basic test example.
     * @group CrudCudOnly
     * @return void
     * @throws Exception
     */
    public function testTopLevelSetToAndMutate():void
    {
        $em = $this->em();
        $conn = $em->getConnection();
        $conn->beginTransaction();
        try {
            /** @var AlbumRepository $albumRepo */
            $albumRepo = $this->em->getRepository(Album::class);
            $arrayHelper = $this->makeArrayHelper();
            //Test as super admin level permissions to be able to create everything in one fell swoop
            $albumRepo->init($arrayHelper, ['testTopLevelSetToAndMutate'], ['testing']);
            /** @var Album[] $result */
            $result = $albumRepo->create($this->createData());
            $album = $result[0];
            $this->assertEquals($album->getName(), 'foobar');
            $em->flush();
            $conn->rollBack();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }



    /**
     * A basic test example.
     * @group CrudCudOnly
     * @return void
     * @throws Exception
     */
    public function testValidatorWorks():void
    {
        $em = $this->em();
        $conn = $em->getConnection();
        $conn->beginTransaction();
        try {
            /** @var AlbumRepository $albumRepo */
            $albumRepo = $this->em->getRepository(Album::class);
            $arrayHelper = $this->makeArrayHelper();
            //Test as super admin level permissions to be able to create everything in one fell swoop
            $albumRepo->init($arrayHelper, ['admin'], ['testing']);
            $e = NULL;
            /** @var Album[] $result */
            try {
                $createData = $this->createData();
                $createData[0]['name'] = 'f';
                $albumRepo->create($createData);
            } catch (Exception $e) {

            }
            $this->assertEquals(get_class($e), EntityException::class);

            $conn->rollBack();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }

    /**
     * A basic test example.
     * @group CrudCudOnly
     * @return void
     * @throws Exception
     */
    public function testCreateAlbumAndArtistAndAddUserToAlbum():void
    {
        $em = $this->em();
        $conn = $em->getConnection();
        $conn->beginTransaction();
        try {
            /** @var AlbumRepository $albumRepo */
            $albumRepo = $this->em->getRepository(Album::class);
            $arrayHelper = $this->makeArrayHelper();
            //Test as super admin level permissions to be able to create everything in one fell swoop
            $albumRepo->init($arrayHelper, ['superAdmin'], ['testing']);
            /** @var Album[] $result */
            $result = $albumRepo->create($this->createData());
            $album = $result[0];
            $artist = $album->getArtist();
            $users = $album->getUsers();
            $user = $users[0];
            $this->assertEquals($album->getName(), 'BEETHOVEN: THE COMPLETE PIANO SONATAS');
            $this->assertEquals($artist->getName(), 'BEETHOVEN');
            $this->assertEquals($user->getId(), 1);

            $em->flush();
            $conn->rollBack();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }



    /**
     * A basic test example.
     * @group CrudCudOnly
     * @return void
     * @throws Exception
     */
    public function testAllowedWorks():void
    {
        $em = $this->em();
        $conn = $em->getConnection();
        $conn->beginTransaction();
        try {
            /** @var AlbumRepository $albumRepo */
            $albumRepo = $this->em->getRepository(Album::class);
            $arrayHelper = $this->makeArrayHelper();
            //Test as super admin level permissions to be able to create everything in one fell swoop
            $albumRepo->init($arrayHelper, ['default'], ['default']);
            $e = NULL;
            /** @var Album[] $result */
            try {
                $albumRepo->create($this->createData());
            } catch (Exception $e) {

            }
            $this->assertEquals(get_class($e), EntityArrayHelperException::class);
            $conn->rollBack();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }


    /**
     * A basic test example.
     * @group CrudCudOnly
     * @return void
     * @throws Exception
     */
    public function testPermissiveWorks1():void
    {
        $em = $this->em();
        $conn = $em->getConnection();
        $conn->beginTransaction();
        try {
            /** @var AlbumRepository $albumRepo */
            $albumRepo = $this->em->getRepository(Album::class);
            $arrayHelper = $this->makeArrayHelper();
            //Test as super admin level permissions to be able to create everything in one fell swoop
            $albumRepo->init($arrayHelper, ['testPermissive1'], ['testing']);
            $e = NULL;
            /** @var Album[] $result */
            try {
                $albumRepo->create($this->createData());
            } catch (Exception $e) {

            }
            $this->assertEquals(get_class($e), EntityArrayHelperException::class);
            $conn->rollBack();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }


    /**
     * A basic test example.
     * @group CrudCudOnly
     * @return void
     * @throws Exception
     */
    public function testPermissiveWorks2():void
    {
        $em = $this->em();
        $conn = $em->getConnection();
        $conn->beginTransaction();
        try {
            /** @var AlbumRepository $albumRepo */
            $albumRepo = $this->em->getRepository(Album::class);
            $arrayHelper = $this->makeArrayHelper();
            //Test as super admin level permissions to be able to create everything in one fell swoop
            $albumRepo->init($arrayHelper, ['testPermissive2'], ['testing']);
            /** @var Album[] $result */
            $result = $albumRepo->create($this->createData());
            $album = $result[0];
            $this->assertEquals($album->getName(), 'BEETHOVEN: THE COMPLETE PIANO SONATAS');
            $em->flush();
            $conn->rollBack();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }


}
