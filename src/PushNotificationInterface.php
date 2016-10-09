<?php
namespace nstdio\notymo;

/**
 * Interface PushNotificationInterface
 */
interface PushNotificationInterface
{
    /**
     *
     */
    public function send();

    /**
     * @param $count
     */
    public function setRetryCount($count);

    /**
     * @param MessageInterface $message
     */
    public function enqueue(MessageInterface $message);

    /**
     * @param Connection $wrapper
     */
    public function setStreamWrapper(Connection $wrapper);
}