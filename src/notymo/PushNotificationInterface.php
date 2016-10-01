<?php

/**
 * Interface PushNotificationInterface
 */
interface PushNotificationInterface
{
    public function send();

    public function setToken($token);

    public function setMessage($alert);

    public function setCustomData(array $customData);

    public function setSound($sound);
}