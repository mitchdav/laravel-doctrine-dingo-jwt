<?php

use App\API\V1\Entities\User;
use TempestTools\Common\ArrayExpressions\ArrayExpressionBuilder;
use TempestTools\Common\ArrayObject\DefaultTTArrayObject;
use TempestTools\Common\Constants\CommonArrayObjectKeyConstants;
use TempestTools\Common\Exceptions\ArrayObject\ArrayObjectException;
use TempestTools\Common\Helper\ArrayHelper;

class ArrayHelperTest extends TestCase
{
    /**
     * @group arrayHelper
     * Tests that auto parsing is working
     */
    public function testFixedOnArrayObject()
    {
        $arrayHelper = new ArrayHelper(new DefaultTTArrayObject([CommonArrayObjectKeyConstants::FRAMEWORK_KEY_NAME=>['1'=>true,'2'=>true]]));
        $array = $arrayHelper->getArray();
        $e = null;
        try {
            $array[CommonArrayObjectKeyConstants::FRAMEWORK_KEY_NAME] = 'bam!';
        } catch (\Exception $e) {

        }
        $this->assertInstanceOf( ArrayObjectException::class, $e);
    }

    /**
     * @group arrayHelper
     * Tests that auto parsing is working
     */
    public function testDefaultsOnArrayObject()
    {
        $arrayHelper = new ArrayHelper(new DefaultTTArrayObject());
        $array = $arrayHelper->getArray();
        $keys = CommonArrayObjectKeyConstants::getAll();
        foreach ($keys as $key) {
            $this->assertArrayHasKey($key, $array);
        }
    }

    /**
     * @group arrayHelper
     * Tests that auto parsing is working
     */
    public function testEnforceValues()
    {
        $arrayHelper = new ArrayHelper(new ArrayObject());
        $result = $arrayHelper->testEnforceValues([['foo'=>'bar']], [['foo'=>'bar']]);
        $this->assertTrue($result);
        $result = $arrayHelper->testEnforceValues([['foo'=>'bar']], [['foo'=>'baz']]);
        $this->assertFalse($result);

    }

    /**
     * @group arrayHelper
     * Tests that auto parsing is working
     */
    public function testWrapArrayAndNumeric()
    {
        $arrayHelper = new ArrayHelper(new ArrayObject());
        $result = $arrayHelper->wrapArray(['foo'=>'bar']);
        $this->assertTrue($arrayHelper->isNumeric($result));

    }

    /**
     * @group arrayHelper
     * Tests that auto parsing is working
     */
    public function testFindSetting()
    {
        $arrayHelper = new ArrayHelper(new ArrayObject());
        $result = $arrayHelper->findSetting([['foo'=>'bar'], NULL], 'foo');
        $this->assertSame($result, 'bar');

    }

    /**
     * @group arrayHelper
     * Tests that auto parsing is working
     */
    public function testAutoParse()
    {
        $testArray = $this->getTestArray();
        $arrayHelper = new ArrayHelper($testArray);
        $result1 = $arrayHelper->parse($testArray['base']['goGetIt']);
        $result2 = $arrayHelper->parse($testArray['base']['templateIt']);
        $result3 = $arrayHelper->parse($testArray['base']['closure']);
        $result4 = $arrayHelper->parse($testArray['three']);
        $result5 = $arrayHelper->parse($testArray['base']['closureObject']);
        $result6 = $arrayHelper->parse($testArray['inheritsObject']);
        $result7 = $arrayHelper->parse($testArray['base']['arrayPathObject']);

        $this->assertSame($result1, 'foo');
        $this->assertSame($result2, 'im mary poppins yall!');
        $this->assertTrue($result3);
        $this->assertSame($result4['gotIt'], 'fooBar');
        $this->assertSame($result4['retrieve'], 'foo');
        $this->assertSame($result4['extended'], [':two', ':one', ':base', ':four']);
        $this->assertTrue($result5);
        $this->assertSame($result6['gotIt'], 'fooBar');
        $this->assertSame($result6['retrieve'], 'foo');
        $this->assertSame($result7, 'foo');
    }

    /**
     * @group arrayHelper
     * Test extraction works
     */
    public function testExtraction() {
        $arrayHelper = new ArrayHelper();
        $user = new User();
        $user->setEmail('bobs@youruncle.com');
        $arrayHelper->extract([$user]);
        $array = $arrayHelper->getArray();

        $this->assertSame($array[CommonArrayObjectKeyConstants::USER_KEY_NAME]['email'], 'bobs@youruncle.com');
    }

    /**
     * Get a test array object to work with
     * @return ArrayObject
     */
    protected function getTestArray():ArrayObject {
        return new ArrayObject([
           'base'=> [
               'extends'=>[],
               'closure'=>function ($extra, ArrayHelper $arrayHelper) {
                    return ($arrayHelper instanceof ArrayHelper && is_array($extra));
               },
               'closureObject'=>ArrayExpressionBuilder::closure(function ($extra, ArrayHelper $arrayHelper) {
                   return ($arrayHelper instanceof ArrayHelper && is_array($extra));
               }),
               'arrayPathObject'=>ArrayExpressionBuilder::arrayPath(['one', 'retrieve']),
               'goGetIt'=> ArrayExpressionBuilder::stringPath(':one:retrieve'),
               'templateIt'=>ArrayExpressionBuilder::template('im {{:one:retrieve2}} {{:one:retrieve3}}!'),
               'gotIt'=>'fooBar'
           ],
           'one'=> [
               'extends'=>[':base'],
               'retrieve'=>'foo',
               'retrieve2'=>'mary poppins',
               'retrieve3'=>'yall'
           ],
           'two'=> [
               'extends'=>[':one']
           ],
           'three'=> [
               'extends'=>[':two', ':four']
           ],
           'four'=> [
               'extends'=>[]
           ],
            'inheritsObject'=>ArrayExpressionBuilder::arrayInheritance(
                [
                    'extends'=>[':one'],
                ]
            )
         ]);
    }
}
