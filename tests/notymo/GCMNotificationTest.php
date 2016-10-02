<?php

use nstdio\notymo\GCMNotification;
use nstdio\notymo\MessageInterface;
use nstdio\notymo\PushNotificationInterface;

class GCMNotificationTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PushNotificationInterface
     */
    private $gcm;

    public function setUp()
    {
        $this->gcm = new GCMNotification("api_key");
    }

    public function testEmptyQueue()
    {
        $this->gcm->send();
    }

    public function testSend()
    {
        /** @var \nstdio\notymo\Connection $mockConnection */
        $mockConnection = $this->getMockBuilder('nstdio\notymo\CurlWrapper')->getMock();
        $this->gcm->setStreamWrapper($mockConnection);

        $tokens = array_fill(0, 1500, 1);

        /** @var PHPUnit_Framework_MockObject_MockObject | MessageInterface $message */
        $message = $this->getMockBuilder('nstdio\notymo\Message')
            ->setMethods(array("getToken", "isMultiple", "getCustomData"))
            ->getMock();

        $message->expects($this->exactly(4))
            ->method("getToken")
            ->willReturn($tokens);

        $message->expects($this->exactly(3))
            ->method("isMultiple")
            ->willReturn(true);

        $message->expects($this->exactly(2))
            ->method("getCustomData")
            ->willReturn(array("key_0" => "val_0", "key_1" => "val_1"));

        /** @var PHPUnit_Framework_MockObject_MockObject | MessageInterface $message2 */
        $message2 = $this->getMockBuilder('nstdio\notymo\Message')
            ->setMethods(array("getToken"))
            ->getMock();

        $message2->expects($this->once())
            ->method("getToken")
            ->willReturn(1);

        $this->gcm->enqueue($message);
        $this->gcm->enqueue($message2);

        $this->gcm->send();
    }
}
