<?php
namespace nstdio\notymo;

use ArrayAccess;
use Countable;
use Iterator;
use SplDoublyLinkedList;
use SplQueue;
use Traversable;

/**
 * Class MessageQueue
 *
 * @package nstdio\notymo
 * @author  Edgar Asatryan <nstdio@gmail.com>
 */
class MessageQueue implements Iterator, Countable, ArrayAccess
{
    /**
     * @var SplQueue
     */
    private $queue;

    public function __construct()
    {
        $this->queue = new SplQueue();
        $this->queue->setIteratorMode(SplDoublyLinkedList::IT_MODE_DELETE);
    }

    public function enqueue(MessageInterface $message)
    {
        $this->queue->enqueue($message);
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

    /**
     * @inheritdoc
     */
    public function offsetExists($offset)
    {
        return $this->queue->offsetExists($offset);
    }

    /**
     * @inheritdoc
     */
    public function offsetGet($offset)
    {
        return $this->queue->offsetGet($offset);
    }

    /**
     * @inheritdoc
     */
    public function offsetSet($offset, $value)
    {

    }

    /**
     * @inheritdoc
     */
    public function offsetUnset($offset)
    {
        throw new \RuntimeException("Cannot unset any item.");
    }
}