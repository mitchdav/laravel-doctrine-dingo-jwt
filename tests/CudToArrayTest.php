<?php

use App\API\V1\Entities\Artist;
use App\API\V1\Entities\User;
use App\API\V1\Repositories\ArtistRepository;
use App\API\V1\Repositories\UserRepository;
use TempestTools\Scribe\Orm\Transformers\ToArrayTransformer;
use TempestTools\Scribe\PHPUnit\CrudTestBaseAbstract;


class CudToArrayTest extends CrudTestBaseAbstract
{

    /**
     * @group CudToArray
     * @throws Exception
     */
    public function testToArrayArrayStorage () {
        $em = $this->em();
        $conn = $em->getConnection();
        $conn->beginTransaction();

        try {
            $arrayHelper = $this->makeArrayHelper();
            /** @var ArtistRepository $artistRepo */
            $artistRepo = $this->em->getRepository(Artist::class);
            $artistRepo->init($arrayHelper, ['testing'], ['testing']);
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

            $transformer = new ToArrayTransformer([
                'store'=>false
            ]);
            $transformer->transform($result);

            $stored = $result[0]->getLastToArray();

            //Prove you can not store it

            $this->assertNull($stored);

            $transformer->setSettings([
                'store'=>true
            ]);


            $transformer->transform($result);

            $stored = $result[0]->getLastToArray();

            //Prove you can store it

            $this->assertNotNull($stored);

            $stored['booop!'] = true;
            $result[0]->setLastToArray($stored);

            $transformer->transform($result);

            $stored = $result[0]->getLastToArray();

            //Prove that if not recompute it doesn't recompute it

            $this->assertArrayHasKey('booop!', $stored);

            $transformer->setSettings([
                'useStored'=>false
            ]);

            $transformer->transform($result);

            $stored = $result[0]->getLastToArray();

            //Prove that it can not use the stored on when requested not too

            $this->assertArrayNotHasKey('booop!', $stored);


            $transformer->setSettings([
                'recompute'=>true
            ]);

            $transformer->transform($result);

            $stored = $result[0]->getLastToArray();

            //Prove recompute over writes it

            $this->assertArrayNotHasKey('booop!', $stored);
            
            $conn->rollBack();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }



    /**
     * @group CudToArray
     * @throws Exception
     */
    public function testToArrayBasicFunctionality () {
        $em = $this->em();
        $conn = $em->getConnection();
        $conn->beginTransaction();

        try {
            $arrayHelper = $this->makeArrayHelper();
            /** @var ArtistRepository $artistRepo */
            $artistRepo = $this->em->getRepository(Artist::class);
            $artistRepo->init($arrayHelper, ['testing'], ['testing']);
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

            $transformer = new ToArrayTransformer([
                'recompute'=>true,
                'frontEndOptions'=>[
                    'toArray'=>[
                        'allowOnlyRequestedParams'=>false
                    ]
                ]
            ]);
            $transformed = $transformer->transform($result);

            $this->assertEquals($transformed[0]['name'], 'BEETHOVEN');
            $this->assertEquals($transformed[0]['albums'][0]['name'], 'BEETHOVEN: THE COMPLETE PIANO SONATAS');
            $this->assertCount(2, $transformed[0]['albums']);
            $this->assertTrue(is_string($transformed[0]['albums'][0]['releaseDate']['timezoneName']));
            $this->assertTrue(is_string($transformed[0]['albums'][0]['releaseDate']['formatted']));
            $this->assertTrue(is_int($transformed[0]['albums'][0]['releaseDate']['timestamp']));
            $this->assertTrue(is_int($transformed[0]['albums'][0]['releaseDate']['offset']));
            $this->assertEquals($transformed[0]['albums'][0]['artist']['name'], 'BEETHOVEN');

            $this->assertEquals($transformed[1]['name'], 'BACH');
            $this->assertCount(2, $transformed[1]['albums']);

            $transformer = new ToArrayTransformer([
                'recompute'=>true,
                'frontEndOptions'=>[
                    'toArray'=>[
                        'completeness'=>'limited',
                        'allowOnlyRequestedParams'=>false
                    ]
                ]
            ]);
            $transformed = $transformer->transform($result);

            $this->assertArrayNotHasKey('users', $transformed[0]['albums'][1]);

            $transformer->setSettings([
                'recompute'=>true,
                'frontEndOptions'=>[
                    'toArray'=>[
                        'completeness'=>'minimal',
                        'allowOnlyRequestedParams'=>false
                    ]
                ]
            ]);

            $transformed = $transformer->transform($result);

            $this->assertEmpty( $transformed[0]['albums'][1]);

            $transformer->setSettings([
                'recompute'=>true,
                'frontEndOptions'=>[
                    'toArray'=>[
                        'completeness'=>'none',
                        'allowOnlyRequestedParams'=>false
                    ]
                ]
            ]);

            $transformed = $transformer->transform($result);
            $this->assertEmpty($transformed[0]);

            $transformer->setSettings([
                'recompute'=>true,
                'frontEndOptions'=>[
                    'toArray'=>[
                        'completeness'=>'full',
                        'maxDepth'=>2,
                        'allowOnlyRequestedParams'=>false
                    ]
                ]
            ]);

            $transformed = $transformer->transform($result);

            $this->assertEmpty($transformed[0]['albums'][0]);

            $transformer->setSettings([
                'recompute'=>true,
                'frontEndOptions'=>[
                    'toArray'=>[
                        'excludeKeys'=>['users'],
                        'allowOnlyRequestedParams'=>false
                    ]
                ]
            ]);

            $transformed = $transformer->transform($result);

            $this->assertArrayNotHasKey('users', $transformed[0]['albums'][0]);


            $transformer->setSettings([
                'recompute'=>true,
                'frontEndOptions'=>[
                    'toArray'=>[
                        'completeness'=>'full',
                    ]
                ]
            ]);

            $transformed = $transformer->transform($result);

            $this->assertArrayNotHasKey('email', $transformed[0]['albums'][0]['users'][0]);

            $transformer->setSettings([
                'recompute'=>true,
                'frontEndOptions'=>[
                    'toArray'=>[
                        'completeness'=>'full',
                        'forceIncludeKeys'=>['id', 'email']
                    ]
                ]
            ]);

            $transformed = $transformer->transform($result);

            $this->assertArrayHasKey('email', $transformed[0]['albums'][0]['users'][0]);


            $id = $result[0]->getId();
            $em->clear();

            $result = $artistRepo->update([
                $id => [
                    'name'=>'The artist formerly known as BEETHOVEN',
                ]
            ]);


            $transformer->setSettings([
                'recompute'=>true,
                'frontEndOptions'=>[
                    'toArray'=>[
                        'completeness'=>'full',
                        'allowOnlyRequestedParams'=>false
                    ]
                ]
            ]);

            $transformed = $transformer->transform($result);

            //Make sure eager load not triggered
            $this->assertEmpty($transformed[0]['albums']);

            $em->clear();

            $artistRepo->init($arrayHelper, ['testLazyLoadEnabled'], ['testing']);

            $result = $artistRepo->update([
                $id => [
                    'name'=>'The artist formerly known as BEETHOVEN',
                ]
            ]);

            $transformed = $transformer->transform($result);

            $this->assertCount(2, $transformed[0]['albums']);

            $em->clear();

            $artistRepo->init($arrayHelper, ['testLiteralToArray'], ['testing']);

            $result = $artistRepo->update([
                $id => [
                    'name'=>'The artist formerly known as BEETHOVEN',
                ]
            ]);

            $transformed = $transformer->transform($result);

            $this->assertEquals($transformed[0]['literalString'], 'bob\'s your uncle');
            $this->assertEquals($transformed[0]['literalArrayExpression'], 'literalArrayExpression');
            $this->assertEquals($transformed[0]['literalDateWithFormat']['formatted'], '2001-01-01 00:00:00');

            $array = $arrayHelper->getArray();
            $this->assertArrayHasKey('postToArray', $array['entityEvents']);
            $this->assertArrayHasKey('preToArray', $array['entityEvents']);


            $conn->rollBack();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }




}
