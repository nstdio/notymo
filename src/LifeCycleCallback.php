<?php
namespace nstdio\notymo;
use Closure;

/**
 * Interface LifeCycleListener
 *
 * @package nstdio
 * @author  Edgar Asatryan <nstdio@gmail.com>
 */
interface LifeCycleCallback
{
    /**
     * Will be called when all messages are sent.
     * `$callback` signature: `function(MessageQueue $messages)`
     * Usage
     * ```php
     *
     * $push->onComplete(function(MessageQueue $messages) {
     *
     * });
     *
     * ```
     * @param Closure $callback
     */
    public function onComplete(Closure $callback);

    /**
     * Will be called when the every message was sent.
     * `$callback` signature: `function(MessageInterface $message, array $feedBack)`
     *
     * @param Closure $callback
     */
    public function onEachSent(Closure $callback);

    /**
     * Will be called when error occurs. Note that when error occured and this callback is not defined, an exception will be thrown.
     * `$callback` signature: `function(MessageInterface $message, PushNotificationException $exc)`
     *
     * @param Closure $callback
     */
    public function onError(Closure $callback);

    /**
     * This method has no `Closure` argument because it is not involved in the message
     * sending lifecycle. The single assignment of this method - to remove callbacks.
     * Will be called immediately after `onSent`.
     */
    public function detach();
}