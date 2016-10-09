<?php
namespace nstdio\tests\notymo;

use nstdio\notymo\GCMNotification;

/**
 * Test for common parts.
 * Class AbstractNotificationTest
 *
 * @package nstdio\tests\notymo
 */
class AbstractNotificationTest extends TestCase
{
    /**
     * @var GCMNotification
     */
    private $push;

    public function setUp()
    {
        $this->push = new GCMNotification("api_key");
    }

    /**
     * @dataProvider retryCountProvider
     * @expectedException \InvalidArgumentException
     *
     * @param $count
     */
    public function testRetryCountException($count)
    {
        $this->push->setRetryCount($count);
    }

    public function testSetRetryCount()
    {
        $retryCount = 1;
        $this->push->setRetryCount($retryCount);

        self::assertAttributeEquals($retryCount, 'retryCount', $this->push);

        $this->push->setRetryCount(++$retryCount);

        self::assertAttributeEquals($retryCount, 'retryCount', $this->push);
    }

    public function testSetStreamWrapper()
    {
        $mock = $this->mockConnection();
        $this->push->setStreamWrapper($mock);
        self::assertAttributeEquals($mock, 'stream', $this->push);
    }

    public function testEmptyQueue()
    {
        $this->push->setStreamWrapper($this->mockConnection());
        $counter = 0;

        $this->push->onEachSent(function () use (&$counter) {
            ++$counter;
        });
        $this->push->send();

        self::assertEquals(0, $counter);
    }

    /**
     * @dataProvider connectionProvider
     *
     * @param $response
     */
    public function testQueue($response)
    {
        $mock = $this->mockConnection(array("success" => $response));
        $this->push->setStreamWrapper($mock);

        $messages = array_fill(0, 20, $this->mockMessage(0));
        foreach ($messages as $item) {
            $this->push->enqueue($item);
        }

        $counter = 0;
        $retryCount = 2;
        $this->push->setRetryCount($retryCount);
        $this->push->onEachSent(function () use (&$counter) {
            ++$counter;
        });

        $called = false;
        $this->push->onComplete(function () use (&$called) {
            $called = true;
        });
        $this->push->send();

        if ($response === 1) {
            self::assertAttributeEmpty('messageQueue', $this->push);
            self::assertEquals(count($messages), $counter);
        } else {
            self::assertTrue($called);
            self::assertEquals(count($messages) * $retryCount, $counter);
            self::assertAttributeCount(count($messages), 'messageQueue', $this->push);
        }

    }

    public function testConnectionNullIfQueueEmpty()
    {
        $this->push->send();

        self::assertAttributeEquals(null, 'stream', $this->push);
    }

    public function testDefaultConnection()
    {
        $this->push->enqueue($this->mockMessage(1));
        $this->push->send();

        self::assertAttributeInstanceOf($this->connectionClassName, 'stream', $this->push);
    }

    public function connectionProvider()
    {
        return array(
            "Fail"    => array(0),
            "Success" => array(1),
        );
    }

    public function retryCountProvider()
    {
        return array(
            'int (0)'         => array(0),
            'int (-1)'        => array(-1),
            'string (0)'      => array('0'),
            'string (-1)'     => array('-1'),
            'bool false'      => array(false),
            'array (array())' => array(array()),
            'null'            => array(null),
        );
    }

}
