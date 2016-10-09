<?php
namespace nstdio\notymo;
use nstdio\notymo\exception\UnsupportedNotificationType;

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
            throw new UnsupportedNotificationType("Invalid message type : $type, please use one of MessageInterface constants.");
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