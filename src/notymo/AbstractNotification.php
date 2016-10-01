<?php
namespace nstdio\notymo;

/**
 * Class AbstractNotification
 *
 * @package nstdio\notymo
 * @author  Edgar Asatryan <nstdio@gmail.com>
 */
abstract class AbstractNotification implements PushNotificationInterface
{
    /**
     * @var MessageQueue
     */
    protected $messageQueue;

    /**
     * @var Connection
     */
    protected $stream;

    public function __construct()
    {
        $this->messageQueue = new MessageQueue();
    }

    abstract public function send();

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

    abstract protected function createPayload(MessageInterface $message);

    final protected function openConnection()
    {
        $this->lazyInitStream();

        $this->stream->open($this->getConnectionParams(), null);

        return $this;
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