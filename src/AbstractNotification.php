<?php
namespace nstdio\notymo;

/**
 * Class AbstractNotification
 *
 * @package nstdio\notymo
 * @author  Edgar Asatryan <nstdio@gmail.com>
 */
abstract class AbstractNotification implements PushNotificationInterface, LifeCycleCallback
{
    /**
     * @var MessageQueue
     */
    protected $messageQueue;

    /**
     * @var Connection
     */
    protected $stream;

    /**
     * @var CallbackInvoker
     */
    protected $invoker;

    /**
     * @var int
     */
    protected $retryCount = 3;

    /**
     * AbstractNotification constructor.
     */
    public function __construct()
    {
        $this->messageQueue = new MessageQueue();
        $this->invoker = new CallbackInvoker();
    }

    final public function send()
    {
        if ($this->messageQueue->isEmpty()) {
            return;
        }

        $this->openConnection();

        $attempt = 0;
        while ($attempt < $this->retryCount && !$this->messageQueue->isEmpty()) {
            /** @var MessageInterface $message */
            foreach ($this->messageQueue as $message) {
                $this->sendImpl($message);
            }
            $attempt++;
        }

        $this->notifyOnComplete($this->messageQueue);
        if ($this->messageQueue->isEmpty()) {
            $this->detach();
        }
        $this->stream->close();
    }

    abstract protected function sendImpl(MessageInterface $message);

    public function enqueue(MessageInterface $message)
    {
        $this->messageQueue->enqueue($message);
    }

    /**
     * @param Connection $wrapper
     */
    public function setStreamWrapper(Connection $wrapper)
    {
        $this->stream = $wrapper;
    }

    /**
     * @param int $count
     */
    public function setRetryCount($count)
    {
        $this->retryCount = intval($count);
        if ($this->retryCount <= 0) {
            throw new \InvalidArgumentException("retryCount must be grader then zero.");
        }
    }

    abstract protected function createPayload(MessageInterface $message);

    final protected function openConnection()
    {
        $this->lazyInitStream();

        $this->stream->open($this->getConnectionParams(), null);

        return $this;
    }

    protected function notifyOnComplete(MessageQueue $message)
    {
        $this->invoker->callOnComplete($message);
    }

    protected function notifyOnEachSent(MessageInterface $message, $feedBack)
    {
        $this->invoker->callOnEachSent($message, $feedBack);
    }

    public function onComplete(callable $listener)
    {
        $this->invoker->onComplete($listener);
    }

    public function onEachSent(callable $callback)
    {
        $this->invoker->onEachSent($callback);
    }

    public function detach()
    {
        $this->invoker->detach();
    }

    private function lazyInitStream()
    {
        if ($this->stream === null) {
            $this->stream = new CurlWrapper();
        }
    }

    /**
     * @return array
     */
    abstract protected function getConnectionParams();
}