<?php

use nstdio\notymo\GCMNotification;
use nstdio\notymo\MessageInterface;
use nstdio\notymo\PushNotificationInterface;
use nstdio\tests\notymo\TestCase;

class GCMNotificationTest extends TestCase
{
    /**
     * @var \nstdio\notymo\LifeCycleCallback | PushNotificationInterface
     */
    private $gcm;

    public function setUp()
    {
        $this->gcm = new GCMNotification("api_key");
    }

    public function testSend()
    {
        /** @var PHPUnit_Framework_MockObject_MockObject | \nstdio\notymo\Connection $mockConnection */
        $mockConnection = $this->mockConnection(array("success" => 0));

        $this->gcm->setStreamWrapper($mockConnection);

        $tokens = array_fill(0, 1500, 1);

        /** @var PHPUnit_Framework_MockObject_MockObject | MessageInterface $message */
        $message = $this->mockMessage($tokens, array("key_0" => "val_0", "key_1" => "val_1"));

        /** @var PHPUnit_Framework_MockObject_MockObject | MessageInterface $message2 */
        $message2 = $this->mockMessage(1);

        $this->gcm->enqueue($message);
        $this->gcm->enqueue($message2);

        $this->gcm->send();
    }
}
