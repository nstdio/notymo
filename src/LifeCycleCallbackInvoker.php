<?php
namespace nstdio\notymo;

/**
 * Interface LifeCycleCallbackInvoker
 *
 * @package nstdio\notymo
 * @author  Edgar Asatryan <nstdio@gmail.com>
 */
interface LifeCycleCallbackInvoker
{
    public function callOnComplete(MessageQueue $param);

    public function callOnEachSent(MessageInterface $message, $feedBack);
}