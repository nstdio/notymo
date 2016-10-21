<?php
namespace nstdio\tests\notymo;

use nstdio\notymo\CallbackInvoker;
use nstdio\notymo\exception\PushNotificationException;
use nstdio\notymo\MessageInterface;
use nstdio\notymo\MessageQueue;

class CallbackInvokerTest extends TestCase
{
    /**
     * @var CallbackInvoker
     */
    private $invoker;

    public function setUp()
    {
        $this->invoker = new CallbackInvoker();
    }

    public function testCallbacks()
    {
        $counter = 0;

        $callback = function () use (&$counter) {
            ++$counter;
        };
        $this->invoker->onComplete($callback);

        $this->invoker->callOnComplete(new MessageQueue());
        $this->invoker->callOnComplete(new MessageQueue());
        self::assertEquals(2, $counter);

        $this->invoker->onEachSent($callback);

        $this->invoker->callOnEachSent($this->mockMessage(1));

        self::assertEquals(3, $counter);


        $this->invoker->detach();

        $this->invoker->callOnComplete(new MessageQueue());
        $this->invoker->callOnEachSent($this->mockMessage(0));

        self::assertEquals(3, $counter);
    }

    /**
     * @expectedException \nstdio\notymo\exception\PushNotificationException
     * @expectedExceptionMessage Test msg.
     */
    public function testOnErrorException()
    {
        $this->invoker->callOnError($this->mockMessage(0), new PushNotificationException("Test msg."));
    }

    public function testOnErrorCalled()
    {
        $counter = 0;
        $this2 = $this;
        $errorMessage = 'Error message.';
        $this->invoker->onError(function(MessageInterface $message, PushNotificationException $exc) use (&$counter, &$this2, $errorMessage) {
            ++$counter;
            $this2->assertEquals($errorMessage, $exc->getMessage());
        });

        $this->invoker->callOnError($this->mockMessage(0), new PushNotificationException($errorMessage));
        self::assertEquals(1, $counter);
    }
}
