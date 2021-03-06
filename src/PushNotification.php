<?php
namespace nstdio\notymo;
use Closure;

/**
 * Class PushNotificationComponent
 */
class PushNotification implements PushNotificationInterface, LifeCycleCallback
{
    /**
     * @var PushNotificationInterface[]
     */
    private $notificationImpl = array();

    /**
     * PushNotification constructor.
     *
     * @param array $config Configuration for notification implementations.
     *
     * Available keys.
     * - skipApns Whether use APNS notifications.
     * - skipGcm  Whether use GCM notifications
     * - apns
     *      - live
     *      - cert
     *      - sandboxCert
     * - gcm
     *      - apiKey Google Api key for GCM service.
     */
    public function __construct($config = array())
    {
        $this->notificationImpl = array();

        $this->initApns($config);
        $this->initGcm($config);
    }

    /**
     * @param $config
     */
    private function initApns($config)
    {
        if (!isset($config['skipApns'])) {
            if (!isset($config['apns'])) {
                throw new \InvalidArgumentException("Configuration required for APNSNotification.");
            }

            $args = array_merge(array('live' => false, 'cert' => null, 'sandboxCert' => null), $config['apns']);
            $this->notificationImpl['apns'] = new APNSNotification($args['live'], $args['cert'], $args['sandboxCert']);
        }
    }

    /**
     * @param $config
     */
    private function initGcm($config)
    {
        if (!isset($config['skipGcm'])) {
            if (!isset($config['gcm']) || !isset($config['gcm']['apiKey'])) {
                throw new \InvalidArgumentException("Configuration required for GCMNotification.");
            }
            $this->notificationImpl['gcm'] = new GCMNotification($config['gcm']['apiKey']);
        }
    }

    /**
     *
     */
    public function send()
    {
        $this->invokeMethod('send');
    }

    private function invokeMethod($method, $args = array())
    {
        foreach ($this->notificationImpl as $item) {
            call_user_func_array(array($item, $method), $args);
        }
    }

    public function enqueue(MessageInterface $message)
    {
        if ($message->getType() === MessageInterface::TYPE_IOS && isset($this->notificationImpl['apns'])) {
            $this->notificationImpl['apns']->enqueue($message);
        } elseif ($message->getType() === MessageInterface::TYPE_ANDROID && isset($this->notificationImpl['gcm'])) {
            $this->notificationImpl['gcm']->enqueue($message);
        }
    }

    public function setStreamWrapper(Connection $wrapper)
    {
        throw new \RuntimeException('Not yet implemented.');
    }

    /**
     * @inheritdoc
     */
    public function onComplete(\Closure $param)
    {
        $this->invokeMethod('onSent', array($param));
    }

    /**
     * @inheritdoc
     */
    public function onEachSent(\Closure $callback)
    {
        $this->invokeMethod('onEachSent', array($callback));
    }

    /**
     * Removes all callbacks.
     */
    public function detach()
    {
        $this->invokeMethod('detach');
    }

    /**
     * @param int $count
     */
    public function setRetryCount($count)
    {
        $this->invokeMethod('setRetryCount', array($count));
    }

    /**
     * @inheritdoc
     */
    public function onError(Closure $callback)
    {
        $this->invokeMethod('onError', array($callback));
    }
}
