<?php
namespace nstdio\notymo;

use nstdio\notymo\exception\UnsupportedNotificationTypeException;

/**
 * Class Message
 *
 * @package nstdio\notymo
 * @author  Edgar Asatryan <nstdio@gmail.com>
 */
class Message implements MessageInterface
{
    /**
     * @var string Push notification message.
     */
    private $message;

    /**
     * @var string|array User device tokens
     */
    private $token;

    /**
     * @var array
     */
    private $customData = array();

    /**
     * @var int
     */
    private $badge = 0;

    /**
     * @var string Sound of notification.
     */
    private $sound;

    /**
     * @var int
     */
    private $type;

    /**
     * Creates new ios message.
     *
     * @return MessageInterface
     */
    public static function ios()
    {
        return new Message(self::TYPE_IOS);
    }

    /**
     * Creates new android message.
     *
     * @return MessageInterface
     */
    public static function android()
    {
        return new Message(self::TYPE_ANDROID);
    }

    public function __construct($type = null)
    {
        if ($type !== null) {
            $this->setType($type);
        }
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function setMessage($message)
    {
        $this->message = $message;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function setToken($token)
    {
        if (is_array($token)) {
            $this->token = $token;
        } else {
            $this->token = strval($token);
        }
    }

    public function getCustomData()
    {
        return $this->customData;
    }

    public function setCustomData(array $data)
    {
        $this->customData = $data;
    }

    public function getBadge()
    {
        return $this->badge;
    }

    public function setBadge($badge)
    {
        $this->badge = $badge;
    }

    public function getSound()
    {
        return $this->sound;
    }

    public function setSound($sound)
    {
        $this->sound = $sound;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        if ($type !== self::TYPE_IOS && $type !== self::TYPE_ANDROID) {
            throw new UnsupportedNotificationTypeException("Invalid message type : $type, please use one of MessageInterface constants.");
        }
        $this->type = $type;
    }

    public function addToken($token)
    {
        if ($this->token === null) {
            $this->token = array();
            $this->merge($token);
        } elseif (is_string($this->token)) {
            $oldToken = $this->token;
            $this->token = array();
            array_push($this->token, $oldToken, $token);
        } elseif (is_array($this->token)) {
            $this->merge($token);
        }

    }

    public function isMultiple()
    {
        return is_array($this->token);
    }

    public function cloneWith($type, $tokens)
    {
        if ($type === $this->type) {
            return $this;
        }
        $cloned = clone $this;

        $cloned->setType($type);
        $cloned->token = $tokens;

        return $cloned;
    }

    /**
     * @param $token
     */
    private function merge($token)
    {
        if (is_array($token)) {
            $this->token = array_merge($this->token, $token);
        } else {
            $this->token[] = $token;
        }
    }
}