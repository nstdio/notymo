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
     * @expectedException \nstdio\notymo\exception\UnsupportedNotificationType
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
}
