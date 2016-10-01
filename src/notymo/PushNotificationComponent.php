<?php

/**
 * Class PushNotificationComponent
 * @property APNSNotificationComponent APNSNotification
 * @property GCMNotificationComponent GCMNotification
 */
class PushNotificationComponent extends Component implements PushNotificationInterface
{
    /**
     * @inheritdoc
     */
    public $components = array('GCMNotification', 'APNSNotification');

    /**
     *
     */
    const SOUND_MONEY = 'cha_ching.wav';

    /**
     * @var DeviceToken
     */
    private $deviceTokenModel;

    /**
     * @var UserSettings
     */
    private $userSettings;

    /**
     * @var PushNotificationInterface|PushNotificationInterface[]
     */
    private $notificationImpl;

    /**
     * @var Device tokens for iOS devices
     */
    private $apnsTokens = array();

    /**
     * @var Device tokens for Android devices
     */
    private $gcmTokens = array();

    /**
     * @param Controller $controller
     */
    public function initialize(Controller $controller)
    {
        $this->deviceTokenModel = ClassRegistry::init('DeviceToken');
        $this->userSettings = ClassRegistry::init('UserSettings');
    }

    /**
     * @param $userId
     */
    public function findAndSetDeviceTokens($userId)
    {
        $dt = $this->deviceTokenModel->findAllByUserId($userId, array('device_token', 'gcm_token'));
        foreach ($dt as $value) {
            $deviceToken = (object)$value['DeviceToken'];

            if ($deviceToken->device_token !== null) {
                $this->apnsTokens[] = $deviceToken->device_token;
            }
            if ($deviceToken->gcm_token !== null) {
                $this->gcmTokens[] = $deviceToken->gcm_token;
            }
        }
        $this->initImplementations();
        if (is_array($this->notificationImpl)) {
            foreach ($this->notificationImpl as $key => $impl) {
                if ($this->userSettings->isPushNotificationEnabled($userId)) {
                    $impl->setToken($key === 'apns' ? $this->apnsTokens : $this->gcmTokens);
                }
            }
        }
    }

    /**
     *
     */
    public function send()
    {
        $this->invokeMethod('send');
    }

    /**
     * @param mixed $alert
     */
    public function setAlert($alert)
    {
        $this->invokeMethod('setAlert', array($alert));
    }

    /**
     * Sets notification sound to money earning sound
     */
    public function setSoundMoneySound()
    {
        $this->setSound(self::SOUND_MONEY);
    }

    /**
     * @param array $customData
     */
    public function setCustomData(array $customData)
    {
        $this->invokeMethod('setCustomData', array($customData));
    }

    /**
     * @param $token
     */
    public function setToken($token)
    {
        $this->invokeMethod('setToken', array($token));
    }

    /**
     * @param $sound
     */
    public function setSound($sound)
    {
        $this->invokeMethod('setSound', array($sound));
    }

    private function initImplementations()
    {
        if (!empty($this->gcmTokens) && !empty($this->apnsTokens)) {
            $this->notificationImpl['apns'] = $this->APNSNotification;
            $this->notificationImpl['gcm'] = $this->GCMNotification;
        } elseif (!empty($this->gcmTokens)) {
            $this->notificationImpl = $this->GCMNotification;
            $this->notificationImpl->setToken($this->gcmTokens);
        } elseif (!empty($this->apnsTokens)) {
            $this->notificationImpl = $this->APNSNotification;
            $this->notificationImpl->setToken($this->apnsTokens);
        }
    }

    private function invokeMethod($method, $args = array())
    {
        if (is_array($this->notificationImpl)) {
            foreach ($this->notificationImpl as $item) {
                if (method_exists($item, $method)) {
                    call_user_func_array(array($item, $method), $args);
                }
            }
        } elseif ($this->notificationImpl instanceof PushNotificationInterface) {
            call_user_func_array(array($this->notificationImpl, $method), $args);
        }
    }
}