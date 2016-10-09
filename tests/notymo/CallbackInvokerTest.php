<?php
namespace nstdio\tests\notymo;

use nstdio\notymo\CallbackInvoker;
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
}
