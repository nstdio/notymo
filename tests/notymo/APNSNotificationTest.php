<?php
namespace nstdio\tests\notymo;

use nstdio\notymo\APNSNotification;
use nstdio\notymo\LifeCycleCallback;
use nstdio\notymo\Message;
use nstdio\notymo\MessageInterface;
use nstdio\notymo\PushNotificationInterface;

class APNSNotificationTest extends TestCase
{
    /**
     * @var PushNotificationInterface | LifeCycleCallback
     */
    private $apns;

    public function setUp()
    {
        $this->apns = new APNSNotification(false, null, __DIR__ . '/../cacert.pem');

        $this->apns->setStreamWrapper($this->mockConnection(array()));
    }

    public function testApns()
    {
        $tokens = array(
            $this->simpleApnsTokenGenerator(),
            $this->simpleApnsTokenGenerator(),
        );
        $message = $this->mockMessage($tokens, array("key_0" => "val_0"));

        $message->expects($this->any())
            ->method("getType")
            ->willReturn(MessageInterface::TYPE_IOS);

        $message2 = $this->mockMessage($this->simpleApnsTokenGenerator());

        $message2->expects($this->any())
            ->method("getType")
            ->willReturn(MessageInterface::TYPE_IOS);

        $this->apns->enqueue($message);
        $this->apns->enqueue($message2);

        $this->apns->send();
    }

    /**
     * @expectedException \nstdio\notymo\exception\InvalidCertException
     */
    public function testInvalidCert()
    {
        new APNSNotification(true, 'invalid/path/to/cert.pem');
    }

    /**
     * @expectedException \nstdio\notymo\exception\InvalidCertException
     */
    public function testInvalidSandboxCert()
    {
        new APNSNotification(false, null, 'invalid/path/to/cert.pem');
    }

    public function testSendNotCorrectTypeMeg()
    {
        $message = $this->mockMessage(null);

        $message->expects($this->any())
            ->method("getType")
            ->willReturn(Message::TYPE_ANDROID);

        $this->apns->enqueue($message);

        $counter = 0;
        $this->apns->onEachSent(function () use (&$counter) {
            ++$counter;
        });

        $this->apns->send();

        self::assertEquals(0, $counter);
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
