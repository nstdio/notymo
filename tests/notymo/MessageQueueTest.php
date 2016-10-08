<?php

use nstdio\notymo\MessageInterface;
use nstdio\notymo\MessageQueue;

class MessageQueueTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var MessageQueue
     */
    private $queue;

    private $messageClassName = 'nstdio\notymo\Message';

    public function setUp()
    {
        $this->queue = new MessageQueue();
        self::assertTrue($this->queue->isEmpty());
    }

    public function testIterate()
    {
        $range = range(0, 9);
        $token = "token1";
        foreach ($range as $key => $item) {
            $msgMock = $this->getMockBuilder($this->messageClassName)
                ->setMethods(array("getToken"))
                ->getMock();

            $msgMock->expects($this->once())
                ->method("getToken")
                ->willReturn($token);

            $this->queue->enqueue($msgMock);
        }

        self::assertCount(count($range), $this->queue);

        /** @var MessageInterface $item */
        foreach ($this->queue as $key => $item) {
            self::assertEquals($token, $item->getToken());
            $this->queue->dequeue();
        }

        self::assertEmpty($this->queue);
    }
}
