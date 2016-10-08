<?php
namespace nstdio\notymo;

/**
 * Class CallbackInvoker
 *
 * @package nstdio\notymo
 * @author  Edgar Asatryan <nstdio@gmail.com>
 */
class CallbackInvoker implements LifeCycleCallback, LifeCycleCallbackInvoker
{
    /**
     * @var callable
     */
    protected $onComplete;

    /**
     * @var callable
     */
    protected $onEachSent;

    /**
     * @var callable
     */
    protected $onError;

    public function callOnComplete($param)
    {
        if (isset($this->onComplete)) {
            call_user_func_array($this->onComplete, array($param));
        }
    }

    public function callOnEachSent(MessageInterface $message, $feedBack = null)
    {
        if (isset($this->onEachSent)) {
            call_user_func_array($this->onEachSent, array($message, $feedBack));
        }
    }

    /**
     * @inheritdoc
     */
    public function onComplete(callable $callback)
    {
        $this->onComplete = $callback;
    }

    /**
     * @inheritdoc
     */
    public function onEachSent(callable $callback)
    {
        $this->onEachSent = $callback;
    }

    /**
     * Removes all callbacks.
     * Will be called immediately after `onSent`.
     */
    public function detach()
    {
        unset($this->onComplete, $this->onEachSent, $this->onError);
    }
}