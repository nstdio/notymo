<?php
namespace nstdio\notymo;

/**
 * Interface MessageInterface
 *
 * @package nstdio\notymo
 * @author  Edgar Asatryan <nstdio@gmail.com>
 */
interface MessageInterface
{
    const TYPE_IOS = 0;

    const TYPE_ANDROID = 1;

    public function addToken($token);

    public function isMultiple();

    public function setMessage($message);

    public function getMessage();

    public function setToken($token);

    public function getToken();

    public function setCustomData(array $data);

    public function getCustomData();

    public function setBadge($badge);

    public function getBadge();

    public function setSound($sound);

    public function getSound();

    public function setType($type);

    public function getType();

}