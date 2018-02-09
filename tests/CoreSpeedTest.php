<?php

class TestClass
{
    public function testMethod() {

    }

    public function getArray () {
        return ['what what'];
    }

    public function getFromArg (array $array):string {
        return $array[0];
    }

    public function getFromSelf ():string {
        return $this->getArray()[0];
    }
}

class CoreSpeedTest extends TestCase
{
    /**
     * A basic functional test example.
     *
     * @return void
     */

    /**
     * Creates the application.
     * @group coreSpeedTests
     */
    public function testCoreSpeed():void
    {
        $testObject = new TestClass();
        $start = microtime(true);
        for ($n=0; $n<1000; $n++) {
            $testObject->testMethod();
        }
        print('1000 method calls: ' . (microtime(true) - $start) . PHP_EOL);

        $testObject = new TestClass();
        $testMethod = 'testMethod';
        $start = microtime(true);
        for ($n=0; $n<1000; $n++) {
            $testObject->$testMethod();
        }
        print('1000 method calls by var: ' . (microtime(true) - $start). PHP_EOL);


        $testArray = [];
        $start = microtime(true);
        for ($n=0; $n<1000; $n++) {
            if (isset($testArray['something'])) {

            }
        }
        print('1000 test if is set: ' . (microtime(true) - $start). PHP_EOL);


        $start = microtime(true);
        for ($n=0; $n<1000; $n++) {
            foreach ($testArray['something'] ?? [] as $key => $value) {

            }
        }
        print('1000 test foreach nothing if not set: ' . (microtime(true) - $start). PHP_EOL);

        $array = $testObject->getArray();
        $start = microtime(true);
        for ($n=0; $n<1000; $n++) {
            $testObject->getFromArg($array);
        }
        print('1000 get array from arg: ' . (microtime(true) - $start). PHP_EOL);

        $start = microtime(true);
        for ($n=0; $n<1000; $n++) {
            $testObject->getFromSelf();
        }
        print('1000 get from self: ' . (microtime(true) - $start). PHP_EOL);

    }
}

