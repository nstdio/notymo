<?php
namespace nstdio\notymo;

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
     * @param callable $callback
     */
    public function onComplete(callable $callback);

    /**
     * Will be called when the every message was sent.
     * `$callback` signature: `function(MessageInterface $message, array $feedBack)`
     *
     * @param callable $callback
     */
    public function onEachSent(callable $callback);

    /**
     * Removes all callbacks.
     * Will be called immediately after `onSent`.
     */
    public function detach();
}