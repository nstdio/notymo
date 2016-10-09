<?php
namespace nstdio\notymo;

use nstdio\notymo\exception\PushNotificationException;

/**
 * Interface LifeCycleCallbackInvoker
 *
 * @package nstdio\notymo
 * @author  Edgar Asatryan <nstdio@gmail.com>
 */
interface LifeCycleCallbackInvoker
{
    /**
     * @param MessageQueue $param
     */
    public function callOnComplete(MessageQueue $param);

    /**
     * @param MessageInterface $message
     * @param                  $feedBack
     */
    public function callOnEachSent(MessageInterface $message, $feedBack);

    /**
     * @param MessageInterface          $message
     * @param PushNotificationException $exception
     */
    public function callOnError(MessageInterface $message, PushNotificationException $exception);
}