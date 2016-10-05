<?php

use nstdio\notymo\APNSNotification;
use nstdio\notymo\Message;
use nstdio\notymo\MessageInterface;
use nstdio\notymo\PushNotificationInterface;

class APNSNotificationTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PushNotificationInterface
     */
    private $apns;

    public function setUp()
    {
        $this->apns = new APNSNotification(false, null, __DIR__ . '/../cacert.pem');

        /** @var \nstdio\notymo\Connection $mockConnection */
        $mockConnection = $this->getMockBuilder('nstdio\notymo\CurlWrapper')->getMock();
        $this->apns->setStreamWrapper($mockConnection);
    }

    public function testApns()
    {
        $tokens = array(
            $this->simpleApnsTokenGenerator(),
            $this->simpleApnsTokenGenerator(),
        );
        /** @var PHPUnit_Framework_MockObject_MockObject | MessageInterface $message */
        $message = $this->getMockBuilder('nstdio\notymo\Message')
            ->setMethods(array("getToken", "getType", "isMultiple"))
            ->getMock();

        $message->expects($this->any())
            ->method("getToken")
            ->willReturn($tokens);

        $message->expects($this->any())
            ->method("getType")
            ->willReturn(MessageInterface::TYPE_IOS);

        $message->expects($this->any())
            ->method("isMultiple")
            ->willReturn(is_array($tokens));

        $this->apns->enqueue($message);
        $this->apns->send();
    }

    /**
     * @expectedException nstdio\notymo\exception\InvalidCert
     */
    public function testInvalidCert()
    {
        new APNSNotification(true, 'invalid/path/to/cert.pem');
    }

    /**
     * @expectedException nstdio\notymo\exception\InvalidCert
     */
    public function testInvalidSandboxCert()
    {
        new APNSNotification(false, null, 'invalid/path/to/cert.pem');
    }

    public function testEmpty()
    {
        $this->apns->send();
    }

    private function simpleApnsTokenGenerator()
    {
        $alphabet = "0123456789abcdef";
        $lastPos = strlen($alphabet) - 1;
        $token = '';
        for ($i = 0; $i < 8; $i++) {
            for ($j = 0; $j < 8; $j++) {
                $token .= $alphabet[mt_rand(0, $lastPos)];
            }
            $token .= ' ';
        }

        return "<" . rtrim($token) . ">";
    }
}
