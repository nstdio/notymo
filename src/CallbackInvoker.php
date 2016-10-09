<?php
namespace nstdio\notymo;

use Closure;
use nstdio\notymo\exception\PushNotificationException;

/**
 * Class CallbackInvoker
 *
 * @package nstdio\notymo
 * @author  Edgar Asatryan <nstdio@gmail.com>
 */
class CallbackInvoker implements LifeCycleCallback, LifeCycleCallbackInvoker
{
    /**
     * @var Closure
     */
    protected $onComplete;

    /**
     * @var Closure
     */
    protected $onEachSent;

    /**
     * @var Closure
     */
    protected $onError;

    /**
     * @inheritdoc
     */
    public function callOnComplete(MessageQueue $param)
    {
        if (isset($this->onComplete)) {
            call_user_func_array($this->onComplete, array($param));
        }
    }

    /**
     * @inheritdoc
     */
    public function callOnEachSent(MessageInterface $message, $feedBack = null)
    {
        if (isset($this->onEachSent)) {
            call_user_func_array($this->onEachSent, array($message, $feedBack));
        }
    }

    /**
     * @inheritdoc
     */
    public function callOnError(MessageInterface $message, PushNotificationException $exc)
    {
        if (isset($this->onError)) {
            call_user_func_array($this->onError, array($message, $exc));
        } else {
            throw $exc;
        }
    }

    /**
     * @inheritdoc
     */
    public function onComplete(\Closure $callback)
    {
        $this->onComplete = $callback;
    }

    /**
     * @inheritdoc
     */
    public function onEachSent(\Closure $callback)
    {
        $this->onEachSent = $callback;
    }

    /**
     * @inheritdoc
     */
    public function onError(Closure $callback)
    {
        $this->onError = $callback;
    }

    /**
     * @inheritdoc
     */
    public function detach()
    {
        unset($this->onComplete, $this->onEachSent, $this->onError);
    }
}