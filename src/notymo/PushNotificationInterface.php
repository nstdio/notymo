<?php
namespace nstdio\notymo;

/**
 * Interface PushNotificationInterface
 */
interface PushNotificationInterface
{
    public function send();

    public function enqueue(MessageInterface $message);

    public function setStreamWrapper(Connection $wrapper);
}