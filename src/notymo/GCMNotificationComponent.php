<?php

/**
 * Class GCMNotificationComponent
 */
class GCMNotificationComponent extends Component implements PushNotificationInterface
{
    /**
     *
     */
    const API_KEY = 'AIzaSyAEcBxbuZOj2as2jLA49VNdpiHQt6IYu_I';

    /**
     *
     */
    const GCM_HOST = 'https://gcm-http.googleapis.com/gcm/send';

    /**
     * @var array
     */
    private $headers;

    /**
     * @var
     */
    private $token;

    /**
     * @var
     */
    private $ch;

    /**
     * @var
     */
    private $alert;

    /**
     * @var
     */
    private $sound;

    /**
     * GCMNotificationComponent constructor.
     * @param ComponentCollection $collection
     * @param array $settings
     */
    public function __construct(ComponentCollection $collection, array $settings)
    {
        parent::__construct($collection, $settings);
        $this->headers = array(
            'Authorization: key=' . self::API_KEY,
            'Content-Type: application/json'
        );
        $this->curlInit();
    }

    /**
     *
     */
    public function send()
    {
        $data = array(
            'registration_ids' => $this->token,
            'data' => array(
                'message' => $this->alert,
                'sound' => $this->sound,
            ),
        );
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, json_encode($data));
        $result = curl_exec($this->ch);
    }

    /**
     *
     */
    public function getRemoteSocketAddress()
    {
        return self::GCM_HOST;
    }

    /**
     * @param $token
     */
    public function setToken($token)
    {
        if (is_string($token)) {
            $this->token = $token;
        } elseif (is_array($token)) {
            foreach ($token as $value) {
                $this->token[] = $value;
            }
        }
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
        // TODO: Implement setCustomData() method.
    }

    /**
     * @param $sound
     */
    public function setSound($sound)
    {
        $this->sound = $sound;
    }

    /**
     *
     */
    private function curlInit()
    {
        $this->ch = curl_init();
        curl_setopt($this->ch, CURLOPT_URL, self::GCM_HOST);
        curl_setopt($this->ch, CURLOPT_POST, true);
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
    }
}