<?php

use nstdio\notymo\CallbackInvoker;

class CallbackInvokerTest extends PHPUnit_Framework_TestCase
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

        $this->invoker->callOnComplete(array());
        $this->invoker->callOnComplete(array());
        self::assertEquals(2, $counter);

        $this->invoker->onEachSent(clone $callback);

        $this->invoker->callOnEachSent($this->getMock('\nstdio\notymo\Message'));

        self::assertEquals(3, $counter);


        $this->invoker->detach();

        $this->invoker->callOnComplete(array());
        $this->invoker->callOnEachSent($this->getMock('\nstdio\notymo\Message'));

        self::assertEquals(3, $counter);
    }
}
