<?php

use App\API\V1\Entities\Artist;
use App\API\V1\Entities\User;
use App\API\V1\Repositories\ArtistRepository;
use App\API\V1\Repositories\UserRepository;
use TempestTools\Scribe\PHPUnit\CrudTestBaseAbstract;


class CrudControllerTest extends CrudTestBaseAbstract
{

    /**
     * @return Artist
     */
    protected function makeTestArtist():Artist
    {
        $artist = new Artist();
        $artist->setName('Test Artist');
        $this->em()->persist($artist);
        $this->em()->flush();
        return $artist;
    }

    /**
     * @return Artist
     */
    protected function getDefaultArtist():Artist
    {
        $artist = $artistRepo = $this->em->getRepository(Artist::class)->findOneBy(['name'=>'Brahms']);
        return $artist;
    }
    /**
     * @group CrudController
     * @throws Exception
     */
    public function testCreateUpdateDelete () {
        $em = $this->em();
        $conn = $em->getConnection();
        $conn->beginTransaction();
        $artistRepo = null;
        try {
            $arrayHelper = $this->makeArrayHelper();
            /** @var ArtistRepository $artistRepo */
            $artistRepo = $this->em->getRepository(Artist::class);
            $artistRepo->init($arrayHelper, ['testNullAssignType'], ['testing']);
            /** @var UserRepository $userRepo */
            $userRepo = $this->em->getRepository(User::class);
            $userRepo->init($arrayHelper, ['testing'], ['testing']);

            $testUser = $userRepo->findOneBy(['id'=>1]);

            $response = $this->json('POST', '/auth/authenticate', ['email' => $testUser->getEmail(), 'password' => $testUser->getPassword()]);
            $result = $response->decodeResponseJson();

            /** @var string $token */
            $token = $result['token'];
            $this->refreshApplication();

            $create = [
                'token'=>$token,
                'params'=>[
                    'name'=>'Test Artist'
                ],
                'options'=>[
                    'simplifiedParams'=>true,
                    'testMode'=>true
                ]
            ];
            $response = $this->json('POST', '/contexts/admin/artists', $create, ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();

            $this->assertEquals('Test Artist', $result['name']);


            $create = [
                'token'=>$token,
                'params'=>[
                    [
                        'name'=>'Test Artist'
                    ]
                ],
                'options'=>[
                    'simplifiedParams'=>true,
                    'testMode'=>true
                ]
            ];
            $response = $this->json('POST', '/contexts/admin/artists', $create, ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();

            $this->assertEquals('Test Artist', $result[0]['name']);

            $artist = $this->getDefaultArtist();
            $update = [
                'token'=>$token,
                'params'=>[
                    [
                        'id'=>$artist->getId(),
                        'name'=>'Test Artist Updated'
                    ]
                ],
                'options'=>[
                    'simplifiedParams'=>true,
                    'testMode'=>true
                ]
            ];


            $response = $this->json('PUT', '/contexts/admin/artists/batch', $update, ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();

            $this->assertEquals('Test Artist Updated', $result[0]['name']);

            $update = [
                'token'=>$token,
                'params'=> [
                    'name'=>'Test Artist Updated Again'
                ],
                'options'=>[
                    'testMode'=>true
                ]
            ];


            $response = $this->json('PUT', '/contexts/admin/artists/'. $result[0]['id'], $update, ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();

            $this->assertEquals('Test Artist Updated Again', $result['name']);

            $update = [
                'token'=>$token,
                'params'=> [
                    'name'=>'Test Artist Updated Again And Again'
                ],
                'options'=>[
                    'simplifiedParams'=>true,
                    'testMode'=>true
                ]
            ];


            $response = $this->json('PUT', '/contexts/admin/artists/'. $result['id'], $update, ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();

            $this->assertEquals('Test Artist Updated Again And Again', $result['name']);



            $delete = [
                'token'=>$token,
                'params'=>[
                    [
                        'id'=>$result['id'],
                    ]
                ],
                'options'=>[
                    'simplifiedParams'=>true,
                    'testMode'=>true
                ]
            ];


            $response = $this->json('DELETE', '/contexts/admin/artists/batch', $delete, ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();

            $this->assertNull($result[0]['id']);

            $delete = [
                'token'=>$token,
                'params'=> [
                ],
                'options'=>[
                    'testMode'=>true
                ]
            ];


            $response = $this->json('DELETE', '/contexts/admin/artists/'. $artist->getId(), $delete, ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();

            $this->assertNull($result['id']);

            $delete = [
                'token'=>$token,
                'params'=> [
                ],
                'options'=>[
                    'simplifiedParams'=>true,
                    'testMode'=>true
                ]
            ];


            $response = $this->json('DELETE', '/contexts/admin/artists/'. $artist->getId(), $delete, ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();

            $this->assertNull($result['id']);

            $conn->rollBack();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }


    /**
     * @group CrudController
     * @throws Exception
     */
    public function testRead () {
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

            $testUser = $userRepo->findOneBy(['id'=>1]);

            $response = $this->json('POST', '/auth/authenticate', ['email' => $testUser->getEmail(), 'password' => $testUser->getPassword()]);
            $result = $response->decodeResponseJson();

            /** @var string $token */
            $token = $result['token'];
            $this->refreshApplication();


            $testArtist = $artistRepo->findOneBy(['name'=>'Brahms']);
            $response = $this->json('GET', '/contexts/guest/artists/' . $testArtist->getId(), ['token'=>$token], ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();
            $this->assertEquals($result['name'], 'Brahms');

            $query = [
                'query'=>[
                    'where'=>[
                        [
                            'field'=>'a.name',
                            'type'=>'and',
                            'operator'=>'eq',
                            'arguments'=>['Brahms']
                        ],
                    ]
                ]
            ];

            $response = $this->json('GET', '/contexts/guest/artists?queryLocation=body', array_merge(['token'=>$token], $query), ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();
            $this->assertEquals($result['result'][0]['name'], 'Brahms');

            $response = $this->json('GET', '/contexts/guest/artists?queryLocation=singleParam&query='. json_encode($query), ['token'=>$token], ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();
            $this->assertEquals($result['result'][0]['name'], 'Brahms');


            $response = $this->json('GET', '/contexts/guest/artists?queryLocation=params&and_where_eq_a-name=Brahms', ['token'=>$token], ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();
            $this->assertEquals($result['result'][0]['name'], 'Brahms');

            $conn->rollBack();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }

}
