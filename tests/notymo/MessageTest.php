<?php

use nstdio\notymo\Message;
use nstdio\notymo\MessageInterface;

class MessageTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var MessageInterface
     */
    private $message;

    public function setUp()
    {
        $this->message = new Message();
    }

    public function testAddToken()
    {
        $tokensCount = 20;
        $tokens = array();
        for ($i = 0; $i < $tokensCount; $i++) {
            $tokens[$i] = 'token' . ($i + 1);
            $this->message->addToken($tokens[$i]);
        }


        self::assertTrue($this->message->isMultiple());
        self::assertAttributeCount($tokensCount, 'token', $this->message);
        self::assertEquals($tokens, $this->message->getToken());
    }

    public function testAddTokenWithExistingToken()
    {
        $token1 = "token1";
        $token2 = "token2";

        $this->message->setToken($token1);
        self::assertFalse($this->message->isMultiple());

        $this->message->addToken($token2);
        self::assertTrue($this->message->isMultiple());
        self::assertAttributeCount(2, 'token', $this->message);
        self::assertEquals(array($token1, $token2), $this->message->getToken());
    }

    public function testSetTokenArray()
    {
        $tokens = array("token1", "token2", "token3");
        $lastToken = "token4";

        $this->message->setToken($tokens);

        self::assertTrue($this->message->isMultiple());
        self::assertAttributeCount(count($tokens), 'token', $this->message);
        self::assertEquals($tokens, $this->message->getToken());

        $this->message->addToken($lastToken);
        array_push($tokens, $lastToken);

        self::assertEquals($tokens, $this->message->getToken());

        $this->message->addToken($tokens);
        self::assertEquals(array_merge($tokens, $tokens), $this->message->getToken());

    }

    /**
     * @expectedException \nstdio\notymo\exception\UnsupportedNotificationTypeException
     */
    public function testIncorrectMessageType()
    {
        $this->message->setType(PHP_INT_MAX);
    }

    public function testSetProperties()
    {
        $message = "This is expected message";
        $token = "token";
        $data = array("key_0" => "value_0", "key_1" => "value_1");
        $badge = 5;
        $sound = "default";
        $type = Message::TYPE_IOS;

        $this->message->setMessage($message);
        $this->message->setToken($token);
        $this->message->setCustomData($data);
        $this->message->setBadge($badge);
        $this->message->setSound($sound);
        $this->message->setType($type);

        self::assertEquals($message, $this->message->getMessage());
        self::assertEquals($token, $this->message->getToken());
        self::assertEquals($data, $this->message->getCustomData());
        self::assertEquals($badge, $this->message->getBadge());
        self::assertEquals($sound, $this->message->getSound());
        self::assertEquals($type, $this->message->getType());
    }

    public function testTypeOnCtor()
    {
        $msg = new Message(Message::TYPE_IOS);

        self::assertEquals(Message::TYPE_IOS, $msg->getMessage());
    }

    public function testCloneWithSameType()
    {
        $msg = new Message(Message::TYPE_IOS);

        $msgClone = $msg->cloneWith(Message::TYPE_IOS, array());

        self::assertSame($msg, $msgClone);
    }

    public function testCloneWith()
    {
        $androidTokens = range(0, 999);
        $iOSTokens = range(1000, 1999);

        $msg = new Message(Message::TYPE_ANDROID);
        $msg->setToken($androidTokens);

        $msgClone = $msg->cloneWith(Message::TYPE_IOS, $iOSTokens);

        self::assertNotSame($msgClone, $msg);
        self::assertEquals($androidTokens, $msg->getToken());
        self::assertEquals($iOSTokens, $msgClone->getToken());
        self::assertEquals($msg->getType(), Message::TYPE_ANDROID);
        self::assertEquals($msgClone->getType(), Message::TYPE_IOS);
    }
}
