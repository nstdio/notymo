<?php

/**
 * Class APNSNotificationComponent
 */
class APNSNotificationComponent extends Component implements PushNotificationInterface
{
    /**
     *
     */
    const APNS_HOST = 'gateway.push.apple.com';

    /**
     *
     */
    const APNS_SANDBOX_HOST = 'gateway.sandbox.push.apple.com';

    /**
     *
     */
    const APNS_PORT = 2195;

    /**
     *
     */
    const APNS_CERT = 'apns-production.pem';

    /**
     *
     */
    const APNS_SANDBOX_CERT = 'apns-dev.pem';

    /**
     *
     */
    const STREAM_WRAPPER = 'ssl';

    /**
     * @var bool Connect to APNS sandbox or live server.
     */
    public $live = true;

    /**
     * @var string|array JSON encoded array that will be sent using [[stream]]
     */
    private $payload;

    /**
     * @var
     */
    private $stream;

    /**
     * @var string Push notification message.
     */
    private $alert;

    /**
     * @var string User device token
     */
    private $token;

    /**
     * @var array
     */
    private $customData = array();

    /**
     * @var int
     */
    private $badge = 0;

    /**
     * @var string Sound of notification.
     */
    private $sound = 'default';

    /**
     * @return self
     */
    private function createPayload()
    {
        $this->payload = array();
        $this->payload['aps'] = array(
            'alert' => $this->alert,
            'badge' => $this->badge,
            'sound' => $this->sound,
        );

        foreach ($this->customData as $key => $value) {
            if ($key !== 'aps') {
                $this->payload[$key] = $value;
            }
        }

        $this->payload = json_encode($this->payload);

        return $this;
    }

    /**
     * @throws Exception
     */
    public function send()
    {
        $this->openStream()
            ->createPayload()
            ->write()
            ->close();
    }

    /**
     * @return string Full qualified url address of APNS server.
     */
    private function getRemoteSocketAddress()
    {
        return sprintf("%s://%s:%d", self::STREAM_WRAPPER, $this->live ? self::APNS_HOST : self::APNS_SANDBOX_HOST, self::APNS_PORT);
    }

    /**
     * @param null $token
     * @return string
     */
    private function buildBinMessage($token = null)
    {
        return chr(0) . chr(0) . chr(32) . pack('H*', str_replace(' ', '', $token ? $token : $this->token)) . chr(0) . chr(strlen($this->payload)) . $this->payload;
    }

    /**
     *
     */
    private function openStream()
    {
        $streamCtx = stream_context_create(array(
            'ssl' => array(
                'local_cert' => $this->live ? self::APNS_CERT : self::APNS_SANDBOX_CERT,
            ),
        ));

        $this->stream = stream_socket_client($this->getRemoteSocketAddress(), $error, $errorString, 2, STREAM_CLIENT_CONNECT, $streamCtx);

        if (!is_resource($this->stream)) {
            throw new Exception("Failed to connect: $error $errorString");
        }
        return $this;
    }

    /**
     * @param $token
     */
    public function setToken($token)
    {
        if (is_string($token)) {
            $this->token = $this->removeTags($token);
        } elseif (is_array($token)) {
            foreach ($token as $value) {
                $this->token[] = $this->removeTags($value);
            }
        }
    }

    /**
     * @param $deviceToken
     * @return mixed
     */
    private function removeTags($deviceToken)
    {
        return str_replace(array('<', '>'), '', $deviceToken);
    }

    /**
     * @param $alert
     */
    public function setMessage($alert)
    {
        $this->alert = $alert;
    }

    /**
     * @param array $customData
     */
    public function setCustomData(array $customData)
    {
        $this->customData = $customData;
    }

    /**
     * Writes all data to the stream
     * @return self
     */
    private function write()
    {
        if (is_array($this->token)) {
            foreach ($this->token as $token) {
                $binMsg = $this->buildBinMessage($token);
                fwrite($this->stream, $binMsg);
            }
        } else {
            $binMsg = $this->buildBinMessage();
            fwrite($this->stream, $binMsg);
        }

        return $this;
    }

    public function setSound($sound)
    {
        $this->sound = $sound;
    }

    private function close()
    {
        fclose($this->stream);
    }
}