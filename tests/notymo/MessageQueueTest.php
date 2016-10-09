<?php
namespace nstdio\tests\notymo;

use nstdio\notymo\MessageInterface;
use nstdio\notymo\MessageQueue;

class MessageQueueTest extends TestCase
{
    /**
     * @var MessageQueue
     */
    private $queue;

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
            $msgMock = $this->mockMessage($token);

            $this->queue->enqueue($msgMock);
        }

        self::assertCount(count($range), $this->queue);

        /** @var MessageInterface $item */
        foreach ($this->queue as $key => $item) {
            self::assertEquals($token, $item->getToken());
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
