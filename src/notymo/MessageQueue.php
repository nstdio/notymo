<?php
namespace nstdio\notymo;

use ArrayAccess;
use Countable;
use Iterator;
use SplDoublyLinkedList;
use SplQueue;

/**
 * Class MessageQueue
 *
 * @package nstdio\notymo
 * @author  Edgar Asatryan <nstdio@gmail.com>
 */
class MessageQueue implements Iterator, Countable
{
    /**
     * @var SplQueue
     */
    private $queue;

    public function __construct()
    {
        $this->queue = new SplQueue();
        $this->queue->setIteratorMode(SplDoublyLinkedList::IT_MODE_FIFO);
    }

    public function enqueue(MessageInterface $message)
    {
        $this->queue->enqueue($message);
    }

    public function dequeue()
    {
        $this->queue->dequeue();
    }

    public function isEmpty()
    {
        return $this->queue->isEmpty();
    }

    /**
     * @inheritdoc
     */
    public function current()
    {
        return $this->queue->current();
    }

    /**
     * @inheritdoc
     */
    public function next()
    {
        $this->queue->next();
    }

    /**
     * @inheritdoc
     */
    public function key()
    {
        return $this->queue->key();
    }

    /**
     * @inheritdoc
     */
    public function valid()
    {
        return $this->queue->valid();
    }

    /**
     * @inheritdoc
     */
    public function rewind()
    {
        $this->queue->rewind();
    }

    /**
     * @inheritdoc
     */
    public function count()
    {
        return count($this->queue);
    }
}