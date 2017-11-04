<?php

use App\API\V1\Entities\Album;
use App\API\V1\Entities\Artist;
use App\API\V1\Entities\User;
use App\API\V1\Repositories\ArtistRepository;
use App\API\V1\Repositories\UserRepository;
use Doctrine\ORM\Query;
use TempestTools\Scribe\Constants\RepositoryEventsConstants;
use TempestTools\Scribe\Exceptions\Orm\Helper\QueryBuilderHelperException;
use TempestTools\Scribe\Exceptions\Orm\Wrapper\QueryBuilderWrapperException;
use TempestTools\Scribe\PHPUnit\CrudTestBaseAbstract;

class CrudReadTest extends CrudTestBaseAbstract
{
    /**
     * @group CrudReadOnly
     * @throws Exception
     */
    public function testGeneralQueryBuildingWithGetParams () {
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

            /** @var Artist[] $result */
            $artistRepo->create($this->createArtistChainData($userIds));
            $artistRepo->init($arrayHelper, ['testQuery'], ['testing']);

            $frontEndQuery = $this->makeTestFrontEndQueryArtistGetParams();
            $frontEndOptions = $this->makeFrontEndQueryOptions();
            $frontEndOptions['useGetParams'] = true;
            $result = $artistRepo->read($frontEndQuery, $frontEndOptions, [
                'hydrate'=>false,
                'placeholders'=>[
                    'placeholderTest3'=>[
                        'value'=>'some stuff3',
                    ],
                    'placeholderTest2'=>[
                        'value'=>'some stuff',
                    ]
                ],
            ]);
            /** @var  \Doctrine\ORM\QueryBuilder $qb */
            /** @var \Doctrine\ORM\Query $query */
            $query = $result['query'];
            $qb = $result['qb'];

            $placeholders = $qb->getParameters();
            $dql = $query->getDQL();

            $this->assertEquals($dql,'SELECT t, a FROM App\API\V1\Entities\Artist t INNER JOIN t.albums a WITH 1 = 1 LEFT JOIN t.albums a2 WITH 1 = 1 WHERE ((((1 = 1 OR 0 <> 1 OR 0 < 1 OR 0 <= 1 OR 1 > 0 OR 1 >= 0 OR t.id IN(1, 0) OR t.id NOT IN(1, 0) OR t.id IS NULL OR t.id IS NOT NULL OR t.name LIKE \'%BEE%\' OR t.name NOT LIKE \'%VAN%\' OR (t.id BETWEEN 0 AND 2)) AND 1 = 1) OR 1 = 1) AND (t.name = :placeholderad553ad84c1ba11a AND t.name <> :placeholdere7646f6929cc4da1) AND t.name = :placeholder5585b8340ac2182b AND t.name = :placeholder250cc8f7b77a15af AND t.name <> :placeholder50ae8bca45384643 AND t.id < :placeholderf30f7d1907f12e32 AND t.id <= :placeholdere9e3789bfb59e910 AND t.id > :placeholder6bb61e3b7bce0931 AND t.id >= :placeholder5d7b9adcbe1c629e AND t.name IN(:placeholder3b9b9e6a2b055833) AND t.name NOT IN(:placeholder1cf3b2433d6e6986) AND t.name IS NULL AND t.name IS NOT NULL AND t.name LIKE :placeholder52bb4eb0974ded8c AND t.name NOT LIKE :placeholderfa7b4ec623968f9a AND (t.id BETWEEN :placeholdercfcd208495d565ef AND :placeholder37ebc6efcc49ae93)) OR (t.name = :placeholder9124f75f1451ed7e OR t.name <> :placeholder13d2d6a6067273d1) GROUP BY t.name, t.name, t.id HAVING (((1 = 1 AND 1 = 1) OR 1 = 1) AND t.name = :placeholder5cde382208614d76) OR t.name = :placeholderf6b05f37a61192d6 ORDER BY t.id DESC, t.name ASC, t.id DESC');

            $offset = $query->getFirstResult();
            $limit = $query->getMaxResults();

            $this->assertEquals($offset, 1);
            $this->assertEquals($limit, 1);

            $placeholderKeysToTest = ['placeholderTest2', 'placeholderTest', 'frontEndTestPlaceholder', 'frontEndTestPlaceholder2', 'placeholderTest3'];
            $placeholderValuesToTest = [
                'some stuff',
                'some stuff2',
                '777',
                'stuff2',
                'some stuff3',
                'BEETHOVEN1',
                'BEETHOVEN2',
                'BEETHOVEN3',
                'BEETHOVEN4',
                'Blink 182',
                '99999991',
                '99999992',
                '-1',
                '-2',
                'BEETHOVEN5',
                ['Vanilla Ice'],
                '%BEETHOV%',
                '%The Ruttles%',
                '0',
                '99999993',
                'BEETHOVEN6',
                'BEETHOVEN7',
                'Bob Marley',
                'Urethra Franklin'
            ];

            $existingKeys = [];
            $existingValues = [];
            //$simplePlaceholderReference = [];

            foreach ($placeholders as $placeholder) {
                $existingKeys[] = $placeholder->getName();
                $existingValues[] = $placeholder->getValue();
                //$simplePlaceholderReference[$placeholder->getName()] = $placeholder->getValue();
            }

            foreach ($placeholderKeysToTest as $key) {
                $this->assertContains($key, $existingKeys);
            }

            foreach ($placeholderValuesToTest as $value) {
                $this->assertContains($value, $existingValues);
            }
            $frontEndQuery['and_where_in_t-name'] = '["BEETHOVEN5"]';
            $frontEndQuery['and_where_between_t-id'] = '[0,99999993]';

            $result = $artistRepo->read($frontEndQuery, $frontEndOptions, [
                'hydrate'=>false,
                'placeholders'=>[
                    'placeholderTest3'=>[
                        'value'=>'some stuff3',
                    ],
                    'placeholderTest2'=>[
                        'value'=>'some stuff',
                    ]
                ],
            ]);
            /** @var  \Doctrine\ORM\QueryBuilder $qb */
            /** @var \Doctrine\ORM\Query $query */
            $query = $result['query'];
            $qb = $result['qb'];

            $placeholders = $qb->getParameters();
            $dql = $query->getDQL();

            $this->assertEquals($dql,'SELECT t, a FROM App\API\V1\Entities\Artist t INNER JOIN t.albums a WITH 1 = 1 LEFT JOIN t.albums a2 WITH 1 = 1 WHERE ((((1 = 1 OR 0 <> 1 OR 0 < 1 OR 0 <= 1 OR 1 > 0 OR 1 >= 0 OR t.id IN(1, 0) OR t.id NOT IN(1, 0) OR t.id IS NULL OR t.id IS NOT NULL OR t.name LIKE \'%BEE%\' OR t.name NOT LIKE \'%VAN%\' OR (t.id BETWEEN 0 AND 2)) AND 1 = 1) OR 1 = 1) AND (t.name = :placeholderad553ad84c1ba11a AND t.name <> :placeholdere7646f6929cc4da1) AND t.name = :placeholder5585b8340ac2182b AND t.name = :placeholder250cc8f7b77a15af AND t.name <> :placeholder50ae8bca45384643 AND t.id < :placeholderf30f7d1907f12e32 AND t.id <= :placeholdere9e3789bfb59e910 AND t.id > :placeholder6bb61e3b7bce0931 AND t.id >= :placeholder5d7b9adcbe1c629e AND t.name IN(:placeholder3b9b9e6a2b055833) AND t.name NOT IN(:placeholder1cf3b2433d6e6986) AND t.name IS NULL AND t.name IS NOT NULL AND t.name LIKE :placeholder52bb4eb0974ded8c AND t.name NOT LIKE :placeholderfa7b4ec623968f9a AND (t.id BETWEEN :placeholdercfcd208495d565ef AND :placeholder37ebc6efcc49ae93)) OR (t.name = :placeholder9124f75f1451ed7e OR t.name <> :placeholder13d2d6a6067273d1) GROUP BY t.name, t.name, t.id HAVING (((1 = 1 AND 1 = 1) OR 1 = 1) AND t.name = :placeholder5cde382208614d76) OR t.name = :placeholderf6b05f37a61192d6 ORDER BY t.id DESC, t.name ASC, t.id DESC');
                                           //SELECT t, a FROM App\API\V1\Entities\Artist t INNER JOIN t.albums a WITH 1 = 1 LEFT JOIN t.albums a2 WITH 1 = 1 WHERE ((((1 = 1 OR 0 <> 1 OR 0 < 1 OR 0 <= 1 OR 1 > 0 OR 1 >= 0 OR t.id IN(1, 0) OR t.id NOT IN(1, 0) OR t.id IS NULL OR t.id IS NOT NULL OR t.name LIKE \'%BEE%\' OR t.name NOT LIKE \'%VAN%\' OR (t.id BETWEEN 0 AND 2)) AND 1 = 1) OR 1 = 1) AND (t.name = :placeholderad553ad84c1ba11a AND t.name <> :placeholdere7646f6929cc4da1) AND t.name = :placeholder5585b8340ac2182b AND t.name = :placeholder250cc8f7b77a15af AND t.name <> :placeholder50ae8bca45384643 AND t.id < :placeholderf30f7d1907f12e32 AND t.id <= :placeholdere9e3789bfb59e910 AND t.id > :placeholder6bb61e3b7bce0931 AND t.id >= :placeholder5d7b9adcbe1c629e AND t.name IN(:placeholderd41d8cd98f00b204) AND t.name NOT IN(:placeholder1cf3b2433d6e6986) AND t.name IS NULL AND t.name IS NOT NULL AND t.name LIKE :placeholder52bb4eb0974ded8c AND t.name NOT LIKE :placeholderfa7b4ec623968f9a AND (t.id BETWEEN :placeholdercfcd208495d565ef AND :placeholder37ebc6efcc49ae93)) OR (t.name = :placeholder9124f75f1451ed7e OR t.name <> :placeholder13d2d6a6067273d1) GROUP BY t.name, t.name, t.id HAVING (((1 = 1 AND 1 = 1) OR 1 = 1) AND t.name = :placeholder5cde382208614d76) OR t.name = :placeholderf6b05f37a61192d6 ORDER BY t.id DESC, t.name ASC, t.id DESC

            $placeholderKeysToTest = ['placeholderTest2', 'placeholderTest', 'frontEndTestPlaceholder', 'frontEndTestPlaceholder2', 'placeholderTest3'];
            $placeholderValuesToTest = [
                'some stuff',
                'some stuff2',
                '777',
                'stuff2',
                'some stuff3',
                'BEETHOVEN1',
                'BEETHOVEN2',
                'BEETHOVEN3',
                'BEETHOVEN4',
                'Blink 182',
                '99999991',
                '99999992',
                '-1',
                '-2',
                'BEETHOVEN5',
                ['Vanilla Ice'],
                '%BEETHOV%',
                '%The Ruttles%',
                '0',
                '99999993',
                'BEETHOVEN6',
                'BEETHOVEN7',
                'Bob Marley',
                'Urethra Franklin'
            ];

            $existingKeys = [];
            $existingValues = [];
            //$simplePlaceholderReference = [];

            foreach ($placeholders as $placeholder) {
                $existingKeys[] = $placeholder->getName();
                $existingValues[] = $placeholder->getValue();
                //$simplePlaceholderReference[$placeholder->getName()] = $placeholder->getValue();
            }

            foreach ($placeholderKeysToTest as $key) {
                $this->assertContains($key, $existingKeys);
            }

            foreach ($placeholderValuesToTest as $value) {
                $this->assertContains($value, $existingValues);
            }

            $conn->rollBack();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }


    /**
     * @group CrudReadOnly
     * @throws Exception
     */
    public function testMutateUsed () {
        $em = $this->em();
        $conn = $em->getConnection();
        $conn->beginTransaction();
        try {
            $arrayHelper = $this->makeArrayHelper();

            $frontEndOptions = $this->makeFrontEndQueryOptions();
            $optionsOverrides = [
                'hydrate'=>false,
                'placeholders'=>[
                    'placeholderTest2'=>[
                        'value'=>'some stuff',
                    ]
                ]
            ];
            /** @var ArtistRepository $artistRepo */
            $artistRepo = $this->em->getRepository(Artist::class);
            $artistRepo->init($arrayHelper, ['testMutateUsed'], ['testing']);

            $result = $artistRepo->read([
                'query'=>[
                    'where'=>[
                        [
                            'field'=>'t.name',
                            'type'=>'and',
                            'operator'=>'eq',
                            'arguments'=>['Where Test']
                        ],
                    ],
                    'having'=>[
                        [
                            'field'=>'t.name',
                            'type'=>'and',
                            'operator'=>'eq',
                            'arguments'=>['Having Test']
                        ],
                    ],
                    'orderBy'=>[
                        't.name'=>'ASC'
                    ],
                    'groupBy'=>[
                        't.name'
                    ],
                    'placeholders'=>[
                        'test'=>[
                            'value'=>'Bobs your uncle',
                            'type'=>'string'
                        ],
                    ]
                ]
            ], $frontEndOptions, $optionsOverrides);

            /** @var  \Doctrine\ORM\QueryBuilder $qb */
            /** @var \Doctrine\ORM\Query $query */
            $qb = $result['qb'];
            $query = $result['query'];
            $placeholders = $qb->getParameters();

            $placeholderKeysToTest = ['testMutated', 'placeholderTest2', 'placeholdere965bd6ecf5bfd36', 'placeholder4e62ad7b76e2daeb'];
            $placeholderValuesToTest = [
                'Bobs your uncle Mutated',
                'some stuff',
                'Where Test Mutated',
                'Having Test Mutated',
            ];

            $existingKeys = [];
            $existingValues = [];
            $simplePlaceholderReference = [];

            foreach ($placeholders as $placeholder) {
                $existingKeys[] = $placeholder->getName();
                $existingValues[] = $placeholder->getValue();
                $simplePlaceholderReference[$placeholder->getName()] = $placeholder->getValue();
            }

            foreach ($placeholderKeysToTest as $key) {
                $this->assertContains($key, $existingKeys);
            }

            foreach ($placeholderValuesToTest as $value) {
                $this->assertContains($value, $existingValues);
            }


            $dql = $query->getDQL();

            $this->assertEquals($dql,'SELECT a FROM App\API\V1\Entities\Artist a WHERE t.name = :placeholdere965bd6ecf5bfd36 GROUP BY t.nameMutated HAVING t.name = :placeholder4e62ad7b76e2daeb ORDER BY t.nameMutated ASC Mutated');

            $conn->rollBack();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }


    /**
     * @group CrudReadOnly
     * @throws Exception
     */
    public function testMutateAndClosure () {
        $em = $this->em();
        $conn = $em->getConnection();
        $conn->beginTransaction();
        try {
            $arrayHelper = $this->makeArrayHelper();

            $frontEndOptions = $this->makeFrontEndQueryOptions();
            $optionsOverrides = [
                'placeholders'=>[
                    /*'placeholderTest2'=>[
                        'value'=>'some stuff',
                    ]*/
                ]
            ];

            /** @var ArtistRepository $artistRepo */
            $artistRepo = $this->em->getRepository(Artist::class);
            $artistRepo->init($arrayHelper, ['testTopLevelMutateAndClosure'], ['testing']);

            $e = null;
            try {
                $artistRepo->read([
                ], $frontEndOptions, $optionsOverrides);
            } catch (Exception $e) {

            }

            $this->assertInstanceOf(QueryBuilderHelperException::class, $e);
            $this->assertEquals('Error: A validation closure did not pass while building query.', $e->getMessage());


            /** @var ArtistRepository $artistRepo */
            $artistRepo = $this->em->getRepository(Artist::class);
            $artistRepo->init($arrayHelper, ['testMutateAndClosure'], ['testing']);

            $e = null;
            try {
                $artistRepo->read([
                    'query'=>[
                        'where'=>[
                            [
                                'field'=>'t.name',
                                'type'=>'and',
                                'operator'=>'eq',
                                'arguments'=>['BEETHOVEN7']
                            ],
                        ],
                    ]
                ], $frontEndOptions, $optionsOverrides);
            } catch (Exception $e) {

            }

            $this->assertInstanceOf(QueryBuilderHelperException::class, $e);
            $this->assertEquals('Error: A validation closure did not pass while building query.', $e->getMessage());

            $e = null;
            try {
                $artistRepo->read([
                    'query'=>[
                        'having'=>[
                            [
                                'field'=>'t.name',
                                'type'=>'and',
                                'operator'=>'eq',
                                'arguments'=>['BEETHOVEN7']
                            ],
                        ],
                    ]
                ], $frontEndOptions, $optionsOverrides);
            } catch (Exception $e) {

            }

            $this->assertInstanceOf(QueryBuilderHelperException::class, $e);
            $this->assertEquals('Error: A validation closure did not pass while building query.', $e->getMessage());

            $e = null;
            try {
                $artistRepo->read([
                    'query'=>[
                        'orderBy'=>[
                            't.name'=>'ASC'
                        ]
                    ]
                ], $frontEndOptions, $optionsOverrides);
            } catch (Exception $e) {

            }

            $this->assertInstanceOf(QueryBuilderHelperException::class, $e);
            $this->assertEquals('Error: A validation closure did not pass while building query.', $e->getMessage());


            $e = null;
            try {
                $artistRepo->read([
                    'query'=>[
                        'groupBy'=>[
                            't.name'
                        ]
                    ]
                ], $frontEndOptions, $optionsOverrides);
            } catch (Exception $e) {

            }

            $this->assertInstanceOf(QueryBuilderHelperException::class, $e);
            $this->assertEquals('Error: A validation closure did not pass while building query.', $e->getMessage());

            $e = null;
            try {
                $artistRepo->read([
                    'query'=>[
                        'placeholders'=>[
                            'test'=>[
                                'value'=>'Bobs your uncle',
                                'type'=>'string'
                            ],
                        ]
                    ]
                ], $frontEndOptions, $optionsOverrides);
            } catch (Exception $e) {

            }

            $this->assertInstanceOf(QueryBuilderHelperException::class, $e);
            $this->assertEquals('Error: A validation closure did not pass while building query.', $e->getMessage());



            $conn->rollBack();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }

    /**
     * @group CrudReadOnly
     * @throws Exception
     */
    public function testReadPermissions3 () {
        $em = $this->em();
        $conn = $em->getConnection();
        $conn->beginTransaction();
        try {
            $arrayHelper = $this->makeArrayHelper();

            $frontEndOptions = $this->makeFrontEndQueryOptions();
            $optionsOverrides = [
                'placeholders'=>[
                    'placeholderTest2'=>[
                        'value'=>'some stuff',
                    ]
                ]
            ];
            /** @var ArtistRepository $artistRepo */
            $artistRepo = $this->em->getRepository(Artist::class);
            $artistRepo->init($arrayHelper, ['testPermissiveAllowPermissions'], ['testing']);

            $e = null;
            try {
                $artistRepo->read([
                    'query'=>[
                        'groupBy'=>[
                            't.name'
                        ]
                    ]
                ], $frontEndOptions, $optionsOverrides);
            } catch (Exception $e) {

            }

            $this->assertInstanceOf(QueryBuilderHelperException::class, $e);
            $this->assertEquals('Error: Group by not allowed. field = t.name', $e->getMessage());

            $e = null;
            try {
                $artistRepo->read([
                    'query'=>[
                        'placeholders'=>[
                            'test'=>[
                                'value'=>'Bobs your uncle',
                                'type'=>'string'
                            ],
                        ]
                    ]
                ], $frontEndOptions, $optionsOverrides);
            } catch (Exception $e) {

            }

            $this->assertInstanceOf(QueryBuilderHelperException::class, $e);
            $this->assertEquals('Error: You do not have access requested placeholder. placeholder = test', $e->getMessage());



            $conn->rollBack();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }


    /**
     * @group CrudReadOnly
     * @throws Exception
     */
    public function testReadPermissions2 () {
        $em = $this->em();
        $conn = $em->getConnection();
        $conn->beginTransaction();
        try {
            $arrayHelper = $this->makeArrayHelper();

            $frontEndOptions = $this->makeFrontEndQueryOptions();
            $optionsOverrides = [
                'placeholders'=>[
                    'placeholderTest2'=>[
                        'value'=>'some stuff',
                    ]
                ]
            ];
            /** @var ArtistRepository $artistRepo */
            $artistRepo = $this->em->getRepository(Artist::class);
            $artistRepo->init($arrayHelper, ['testNonWherePermissions'], ['testing']);
            $e = null;
            try {
                $artistRepo->read([
                    'query'=>[
                        'having'=>[
                            [
                                'field'=>'t.name',
                                'type'=>'and',
                                'operator'=>'eq',
                                'arguments'=>['BEETHOVEN7']
                            ],
                        ],
                    ]
                ], $frontEndOptions, $optionsOverrides);
            } catch (Exception $e) {

            }

            $this->assertInstanceOf(QueryBuilderHelperException::class, $e);
            $this->assertEquals('Error: Operator not allowed. field = t.name, operator = eq', $e->getMessage());

            $e = null;
            try {
                $artistRepo->read([
                    'query'=>[
                        'orderBy'=>[
                            't.name'=>'ASC'
                        ]
                    ]
                ], $frontEndOptions, $optionsOverrides);
            } catch (Exception $e) {

            }

            $this->assertInstanceOf(QueryBuilderHelperException::class, $e);
            $this->assertEquals('Error: Order by not allowed. field = t.name, direction = ASC', $e->getMessage());


            $e = null;
            try {
                $artistRepo->read([
                    'query'=>[
                        'groupBy'=>[
                            't.name'
                        ]
                    ]
                ], $frontEndOptions, $optionsOverrides);
            } catch (Exception $e) {

            }

            $this->assertInstanceOf(QueryBuilderHelperException::class, $e);
            $this->assertEquals('Error: Group by not allowed. field = t.name', $e->getMessage());

            $e = null;
            try {
                $artistRepo->read([
                    'query'=>[
                        'placeholders'=>[
                            'test'=>[
                                'value'=>'Bobs your uncle',
                                'type'=>'string'
                            ],
                        ]
                    ]
                ], $frontEndOptions, $optionsOverrides);
            } catch (Exception $e) {

            }

            $this->assertInstanceOf(QueryBuilderHelperException::class, $e);
            $this->assertEquals('Error: You do not have access requested placeholder. placeholder = test', $e->getMessage());



            $conn->rollBack();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }


    /**
     * @group CrudReadOnly
     * @throws Exception
     */
    public function testReadPermissions () {
        $em = $this->em();
        $conn = $em->getConnection();
        $conn->beginTransaction();
        try {
            $arrayHelper = $this->makeArrayHelper();

            $frontEndQuery = $this->makeTestFrontEndQueryArtist();
            $frontEndOptions = $this->makeFrontEndQueryOptions();
            $optionsOverrides = [
                'placeholders'=>[
                    'placeholderTest2'=>[
                        'value'=>'some stuff',
                    ]
                ]
            ];
            /** @var ArtistRepository $artistRepo */
            $artistRepo = $this->em->getRepository(Artist::class);
            $artistRepo->init($arrayHelper, ['testAllowed'], ['testing']);
            $e = null;
            try {
                $artistRepo->read($frontEndQuery, $frontEndOptions, $optionsOverrides);
            } catch (Exception $e) {

            }

            $this->assertInstanceOf(QueryBuilderHelperException::class, $e);

            $artistRepo->init($arrayHelper, ['testPermissions1'], ['testing']);

            $e = null;
            try {
                $artistRepo->read($frontEndQuery, $frontEndOptions, $optionsOverrides);
            } catch (Exception $e) {

            }

            $this->assertInstanceOf(QueryBuilderHelperException::class, $e);
            $this->assertEquals('Error: Operator not allowed. field = t.name, operator = eq', $e->getMessage());


            $artistRepo->init($arrayHelper, ['testPermissions2'], ['testing']);

            $e = null;
            try {
                $artistRepo->read($frontEndQuery, $frontEndOptions, $optionsOverrides);
            } catch (Exception $e) {

            }

            $this->assertInstanceOf(QueryBuilderHelperException::class, $e);
            $this->assertEquals('Error: Operator not allowed. field = t.id, operator = lt', $e->getMessage());


            $artistRepo->init($arrayHelper, ['testPermissions3'], ['testing']);

            $e = null;
            try {
                $artistRepo->read($frontEndQuery, $frontEndOptions, $optionsOverrides);
            } catch (Exception $e) {

            }

            $this->assertInstanceOf(QueryBuilderHelperException::class, $e);
            $this->assertEquals('Error: Operator not allowed. field = t.id, operator = gt', $e->getMessage());


            $e = null;
            try {
                $artistRepo->read([
                    'query'=>[
                        'where'=>[
                            [
                                'field'=>'t.name',
                                'type'=>'and',
                                'operator'=>'not a freaking operator',
                                'arguments'=>['no arguments about it']
                            ],
                        ]
                    ]
                ], $frontEndOptions, $optionsOverrides);
            } catch (Exception $e) {

            }

            $this->assertInstanceOf(QueryBuilderWrapperException::class, $e);
            $this->assertEquals('Error: Requested operator is not safe to use. operator = not a freaking operator', $e->getMessage());

            $conn->rollBack();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }

    /**
     * @group CrudReadOnly
     * @throws Exception
     */
    public function testSqlQueryFunctionality () {
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

            /** @var Artist[] $result */
            $artistRepo->create($this->createArtistChainData($userIds));
            $artistRepo->init($arrayHelper, ['testSqlQuery'], ['testing']);

            $frontEndQuery = $this->makeTestFrontEndQueryArtist3();
            $frontEndOptions = $this->makeFrontEndQueryOptions();
            $frontEndOptions['offset'] = 0;
            $optionsOverrides = [
                'hydrate'=>false,
                'paginate'=>false,
                'hydrationType'=>null,
                'placeholders'=>[
                    'placeholderTest3'=>[
                        'value'=>'some stuff3',
                    ],
                    'placeholderTest2'=>[
                        'value'=>'some stuff',
                    ]
                ],
            ];
            $result = $artistRepo->read($frontEndQuery, $frontEndOptions, $optionsOverrides);

            $optionsOverrides['hydrate'] = true;

            /** @var  \Doctrine\ORM\QueryBuilder $qb */
            /** @var \Doctrine\ORM\Query $query */
            $qb = $result['qb'];

            /** @var Doctrine\ORM\Query\Parameter[] $placeholders */
            $placeholders = $qb->getParameters();
            $sql = $qb->getSQL();

            $this->assertInstanceOf(\Doctrine\DBAL\Query\QueryBuilder::class, $qb);

            $this->assertEquals('SELECT * FROM artists t INNER JOIN albums a ON a.artist_id = t.id LEFT JOIN albums a2 ON a.artist_id = t.id WHERE (1 = 1) AND ((:placeholderTest2 = \'some stuff\') AND (:placeholderTest = \'some stuff2\') AND (:frontEndTestPlaceholder = 777) AND (:frontEndTestPlaceholder2 = \'stuff2\') AND (:placeholderTest3 = \'some stuff3\')) AND ((t.name <> :placeholder2ed1f0f3fe3b000e) AND (t.name <> :placeholdere7646f6929cc4da1) AND (t.name = :placeholdere8252331af095c5b)) AND ((t.name <> :placeholder13d2d6a6067273d1) AND (t.name <> :placeholderd0c2158016a373e3)) AND (t.name <> :placeholder7689c193d2472a87) HAVING (1 = 1) OR (t.id <> :placeholdercfcd208495d565ef) OR (t.id <> :placeholdercfcd208495d565ef) LIMIT 1 OFFSET 0', $sql);
            $placeholderKeysToTest = ['placeholderTest2', 'placeholderTest', 'frontEndTestPlaceholder', 'frontEndTestPlaceholder2', 'placeholderTest3'];
            $placeholderValuesToTest = [
                'some stuff',
                'some stuff3',
                777,
                'stuff2',
                'some stuff3',
                0,
                0,
                'Gossepi the squid',
                'Khaaan!!',
                'Urethra Franklin',
                'Bob Marley',
                'BEETHOVEN',
            ];

            $existingKeys = [];
            $existingValues = [];
            $simplePlaceholderReference = [];

            foreach ($placeholders as $key => $value) {
                $existingKeys[] = $key;
                $existingValues[] = $value;
                $simplePlaceholderReference[$key] = $value;
            }

            foreach ($placeholderKeysToTest as $key) {
                $this->assertContains($key, $existingKeys);
            }

            foreach ($placeholderValuesToTest as $value) {
                $this->assertContains($value, $existingValues);
            }

            $result = $artistRepo->read($frontEndQuery, $frontEndOptions, $optionsOverrides);

            $this->assertEquals($result['result'][0]['name'], 'Brahms: Complete Edition');

            $artistRepo->init($arrayHelper, ['testSqlQueryNoCache'], ['testing']);
            $result = $artistRepo->read($frontEndQuery, $frontEndOptions, $optionsOverrides);

            $this->assertEquals($result['result'][0]['name'], 'Brahms: Complete Edition');

            $conn->rollBack();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }




    /**
     * @group CrudReadOnly
     * @throws Exception
     */
    public function testGeneralDataRetrieval () {
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

            /** @var Artist[] $result */
            $artistRepo->create($this->createArtistChainData($userIds));
            $artistRepo->init($arrayHelper, ['testQuery2'], ['testing']);

            $frontEndQuery = $this->makeTestFrontEndQueryArtist2();
            $frontEndOptions = $this->makeFrontEndQueryOptions();
            $frontEndOptions['offset'] = 0;
            $optionsOverrides = [
                'hydrate'=>false,
                'placeholders'=>[
                    'placeholderTest3'=>[
                        'value'=>'some stuff3',
                    ],
                    'placeholderTest2'=>[
                        'value'=>'some stuff',
                    ]
                ],
            ];
            $result = $artistRepo->read($frontEndQuery, $frontEndOptions, $optionsOverrides);

            $optionsOverrides['hydrate'] = true;

            /** @var  \Doctrine\ORM\QueryBuilder $qb */
            /** @var \Doctrine\ORM\Query $query */
            $query = $result['query'];
            $qb = $result['qb'];
            /** @var Doctrine\ORM\Query\Parameter[] $placeholders */
            $placeholders = $qb->getParameters();
            $dql = $query->getDQL();

            $this->assertEquals('SELECT t, a FROM App\API\V1\Entities\Artist t INNER JOIN t.albums a WITH 1 = 1 LEFT JOIN t.albums a2 WITH 1 = 1 WHERE 1 = 1 AND (:placeholderTest2 = \'some stuff\' AND :placeholderTest = \'some stuff2\' AND :frontEndTestPlaceholder = 777 AND :frontEndTestPlaceholder2 = \'stuff2\' AND :placeholderTest3 = \'some stuff3\') AND (t.name <> :placeholder2ed1f0f3fe3b000e AND t.name <> :placeholdere7646f6929cc4da1 AND t.name IN(:placeholder113332c42840c13f)) AND (t.name <> :placeholder13d2d6a6067273d1 AND t.name <> :placeholderd0c2158016a373e3) AND t.name <> :placeholder7689c193d2472a87 HAVING 1 = 1 OR t.id <> :placeholdercfcd208495d565ef OR t.id <> :placeholdercfcd208495d565ef', $dql);


            $placeholderKeysToTest = ['placeholderTest2', 'placeholderTest', 'frontEndTestPlaceholder', 'frontEndTestPlaceholder2', 'placeholderTest3'];
            $placeholderValuesToTest = [
                'some stuff',
                'some stuff3',
                777,
                'stuff2',
                'some stuff3',
                0,
                0,
                'Gossepi the squid',
                'Khaaan!!',
                'Urethra Franklin',
                'Bob Marley',
                'BEETHOVEN',
            ];

            $existingKeys = [];
            $existingValues = [];
            $simplePlaceholderReference = [];

            foreach ($placeholders as $placeholder) {
                $existingKeys[] = $placeholder->getName();
                $existingValues[] = $placeholder->getValue();
                $simplePlaceholderReference[$placeholder->getName()] = $placeholder->getValue();
            }

            foreach ($placeholderKeysToTest as $key) {
                $this->assertContains($key, $existingKeys);
            }

            foreach ($placeholderValuesToTest as $value) {
                $this->assertContains($value, $existingValues);
            }

            $foundArtists = [];
            $foundAlbums = [];
            $result = $artistRepo->read($frontEndQuery, $frontEndOptions, $optionsOverrides);
            $foundArtists[] = $result['result'][0]['name'];
            $foundAlbums[] = $result['result'][0]['albums'][0]['name'];

            $frontEndOptions['offset'] = 1;

            $result = $artistRepo->read($frontEndQuery, $frontEndOptions, $optionsOverrides);

            $foundArtists[] = $result['result'][0]['name'];
            $foundAlbums[] = $result['result'][0]['albums'][0]['name'];


            $this->assertContains('BEETHOVEN', $foundArtists);
            $this->assertContains('Brahms', $foundArtists);

            $this->assertContains('BEETHOVEN: THE COMPLETE PIANO SONATAS', $foundAlbums);
            $this->assertContains('Brahms: Complete Edition', $foundAlbums);

            $optionsOverrides['hydrationType'] = Query::HYDRATE_OBJECT;
            $optionsOverrides['paginate'] = false;
            $result = $artistRepo->read($frontEndQuery, $frontEndOptions, $optionsOverrides);

            $this->assertNull($result['count']);

            $this->assertInstanceOf(Artist::class, $result['result'][0]);

            $optionsOverrides['hydrationType'] = Query::HYDRATE_ARRAY;
            $optionsOverrides['paginate'] = true;
            $frontEndOptions['returnCount'] = false;
            $result = $artistRepo->read($frontEndQuery, $frontEndOptions, $optionsOverrides);

            $this->assertNull($result['count']);

            $frontEndOptions['returnCount'] = true;
            $frontEndOptions['offset'] = 0;
            $frontEndOptions['limit'] = 2;
            $result1 = $artistRepo->read($frontEndQuery, $frontEndOptions, $optionsOverrides);

            $optionsOverrides['fetchJoin'] = false;

            $result2 = $artistRepo->read($frontEndQuery, $frontEndOptions, $optionsOverrides);

            $this->assertCount(2, $result1['result']);

            $this->assertCount(2, $result2['result']);

            $optionsOverrides['maxLimit'] = 1;
            $e = null;
            try {
                $artistRepo->read($frontEndQuery, $frontEndOptions, $optionsOverrides);
            } catch (\Exception $e) {

            }

            $this->assertInstanceOf( QueryBuilderHelperException::class, $e);


            $optionsOverrides['maxLimit'] = 100;

            $optionsOverrides['queryMaxParams'] = 1;

            try {
                $artistRepo->read($frontEndQuery, $frontEndOptions, $optionsOverrides);
            } catch (\Exception $e) {

            }

            $this->assertInstanceOf( QueryBuilderHelperException::class, $e);

            /** @noinspection NullPointerExceptionInspection */
            $array = $artistRepo->getArrayHelper()->getArray()->getArrayCopy();
            foreach ([
                 RepositoryEventsConstants::PRE_READ,
                 RepositoryEventsConstants::VALIDATE_READ,
                 RepositoryEventsConstants::VERIFY_READ,
                 RepositoryEventsConstants::PROCESS_RESULTS_READ,
                 RepositoryEventsConstants::POST_READ
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
     * @group CrudReadOnly
     * @throws Exception
     */
    public function testGeneralQueryBuilding () {
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

            /** @var Artist[] $result */
            $artistRepo->create($this->createArtistChainData($userIds));
            $artistRepo->init($arrayHelper, ['testQuery'], ['testing']);

            $frontEndQuery = $this->makeTestFrontEndQueryArtist();
            $frontEndOptions = $this->makeFrontEndQueryOptions();
            $result = $artistRepo->read($frontEndQuery, $frontEndOptions, [
                'hydrate'=>false,
                'placeholders'=>[
                    'placeholderTest3'=>[
                        'value'=>'some stuff3',
                    ],
                    'placeholderTest2'=>[
                        'value'=>'some stuff',
                    ]
                ],
            ]);
            /** @var  \Doctrine\ORM\QueryBuilder $qb */
            /** @var \Doctrine\ORM\Query $query */
            $query = $result['query'];
            $qb = $result['qb'];
            $paginator = $result['paginator'];
            $qbWrapper = $result['qbWrapper'];
            /** @var Doctrine\ORM\Query\Parameter[] $placeholders */
            $placeholders = $qb->getParameters();
            $dql = $query->getDQL();

            $this->assertInstanceOf(\Doctrine\ORM\Query::class, $query);
            $this->assertInstanceOf(\Doctrine\ORM\QueryBuilder::class, $qb);
            $this->assertInstanceOf(\Doctrine\ORM\Tools\Pagination\Paginator::class, $paginator);
            $this->assertInstanceOf(\TempestTools\Scribe\Doctrine\Wrapper\QueryBuilderDqlWrapper::class, $qbWrapper);
            $lifeTime = $query->getResultCacheLifetime();

            $cacheId = $query->getResultCacheId();

            $this->assertEquals($lifeTime, 777);
            $this->assertEquals($cacheId, 'test_cache_id');
            $queryCacheDrive = $query->getQueryCacheDriver();
            $resultCacheDrive = $query->getResultCacheDriver();

            /** @noinspection NullPointerExceptionInspection */
            $arrayCache = $artistRepo->getArrayHelper()->getArray()['arrayCache'];

            $this->assertSame($arrayCache, $queryCacheDrive);
            $this->assertSame($arrayCache, $resultCacheDrive);


            $this->assertEquals($dql,'SELECT t, a FROM App\API\V1\Entities\Artist t INNER JOIN t.albums a WITH 1 = 1 LEFT JOIN t.albums a2 WITH 1 = 1 WHERE ((((1 = 1 OR 0 <> 1 OR 0 < 1 OR 0 <= 1 OR 1 > 0 OR 1 >= 0 OR t.id IN(1, 0) OR t.id NOT IN(1, 0) OR t.id IS NULL OR t.id IS NOT NULL OR t.name LIKE \'%BEE%\' OR t.name NOT LIKE \'%VAN%\' OR (t.id BETWEEN 0 AND 2)) AND 1 = 1) OR 1 = 1) AND (t.name = :placeholderad553ad84c1ba11a AND t.name <> :placeholdere7646f6929cc4da1) AND t.name = :placeholder5585b8340ac2182b AND t.name = :placeholder250cc8f7b77a15af AND t.name <> :placeholder50ae8bca45384643 AND t.id < :placeholderf30f7d1907f12e32 AND t.id <= :placeholdere9e3789bfb59e910 AND t.id > :placeholder6bb61e3b7bce0931 AND t.id >= :placeholder5d7b9adcbe1c629e AND t.name IN(:placeholder3b9b9e6a2b055833) AND t.name NOT IN(:placeholder1cf3b2433d6e6986) AND t.name IS NULL AND t.name IS NOT NULL AND t.name LIKE :placeholder52bb4eb0974ded8c AND t.name NOT LIKE :placeholderfa7b4ec623968f9a AND (t.id BETWEEN :placeholdercfcd208495d565ef AND :placeholder37ebc6efcc49ae93)) OR (t.name = :placeholder9124f75f1451ed7e OR t.name <> :placeholder13d2d6a6067273d1) GROUP BY t.name, t.name, t.id HAVING (((1 = 1 AND 1 = 1) OR 1 = 1) AND t.name = :placeholder5cde382208614d76) OR t.name = :placeholderf6b05f37a61192d6 ORDER BY t.id DESC, t.name ASC, t.id DESC');
            $placeholderKeysToTest = ['placeholderTest2', 'placeholderTest', 'frontEndTestPlaceholder', 'frontEndTestPlaceholder2', 'placeholderTest3'];
            $placeholderValuesToTest = [
                'some stuff',
                'some stuff2',
                '777',
                'stuff2',
                'some stuff3',
                'BEETHOVEN1',
                'BEETHOVEN2',
                'BEETHOVEN3',
                'BEETHOVEN4',
                'Blink 182',
                '99999991',
                '99999992',
                '-1',
                '-2',
                'BEETHOVEN5',
                ['Vanilla Ice'],
                '%BEETHOV%',
                '%The Ruttles%',
                '0',
                '99999993',
                'BEETHOVEN6',
                'BEETHOVEN7',
                'Bob Marley',
                'Urethra Franklin'
            ];

            $existingKeys = [];
            $existingValues = [];
            //$simplePlaceholderReference = [];

            foreach ($placeholders as $placeholder) {
                $existingKeys[] = $placeholder->getName();
                $existingValues[] = $placeholder->getValue();
                //$simplePlaceholderReference[$placeholder->getName()] = $placeholder->getValue();
            }

            foreach ($placeholderKeysToTest as $key) {
                $this->assertContains($key, $existingKeys);
            }

            foreach ($placeholderValuesToTest as $value) {
                $this->assertContains($value, $existingValues);
            }

            $conn->rollBack();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }

    /**
     * @group CrudReadOnly
     * @throws Exception
     */
    public function testBasicRead () {
        $em = $this->em();
        $conn = $em->getConnection();
        $conn->beginTransaction();
        try {
            $arrayHelper = $this->makeArrayHelper();

            /** @var UserRepository $userRepo */
            $userRepo = $this->em->getRepository(User::class);
            $userRepo->init($arrayHelper, ['user'], ['testing']);
            $result = $userRepo->read();
            $this->assertEquals($result['result'][0]['id'], 1);



            $conn->rollBack();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }

    /**
     * @group CrudReadOnly
     * @throws Exception
     */
    public function testFixedLimit () {
        $em = $this->em();
        $conn = $em->getConnection();
        $conn->beginTransaction();
        try {
            $arrayHelper = $this->makeArrayHelper();

            /** @var UserRepository $userRepo */
            $userRepo = $this->em->getRepository(User::class);
            $userRepo->init($arrayHelper, ['user'], ['testing']);

            $e = null;
            $frontEndOptions= [];
            $frontEndOptions['limit'] = 5;
            $frontEndOptions['offset'] = 0;
            $optionsOverrides = [
                'placeholders'=>[
                ]
            ];
            $optionsOverrides['fixedLimit'] = 5;

            $userRepo->read([], $frontEndOptions, $optionsOverrides);

            $frontEndOptions['offset'] = 5;
            $userRepo->read([], $frontEndOptions, $optionsOverrides);

            $optionsOverrides['fixedLimit'] = 4;

            try {
                $userRepo->read([], $frontEndOptions, $optionsOverrides);
            } catch (Exception $e) {

            }

            $this->assertInstanceOf(QueryBuilderHelperException::class, $e);
            $this->assertEquals('Error: Requested limit and offset are not divisible by the fixed limit. limit = 5, offset = 5, fixed limit = 4', $e->getMessage());

            $conn->rollBack();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }



}
