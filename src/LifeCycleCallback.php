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
     * `$callback` signature: `function(MessageInterface $message)`
     * Usage
     * ```php
     *
     * $push->onComplete(function(MessageInterface $message) {
     *
     * });
     *
     * ```
     * @param Closure $callback
     *
     * @return
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
     * Will be called when error occurs.
     * `$callback` signature: `function(MessageInterface $message, PushNotificationException $exc)`
     *
     * @param Closure $callback
     */
    public function onError(Closure $callback);

    /**
     * Removes all callbacks.
     * Will be called immediately after `onSent`.
     */
    public function detach();
}