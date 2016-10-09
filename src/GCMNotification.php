<?php
namespace nstdio\notymo;

use nstdio\notymo\exception\ConnectionException;

/**
 * Class GCMNotificationComponent
 */
class GCMNotification extends AbstractNotification
{
    /**
     *
     */
    const GCM_HOST = 'https://gcm-http.googleapis.com/gcm/send';

    /**
     *
     */
    const MAX_TOKENS = 1000;

    /**
     * @var array
     */
    private $headers;

    /**
     * GCMNotificationComponent constructor.
     *
     * @param $apiKey
     */
    public function __construct($apiKey)
    {
        parent::__construct();

        $this->headers = array(
            'Authorization: key=' . $apiKey,
            'Content-Type: application/json',
        );
    }

    protected function sendImpl(MessageInterface $message)
    {
        $response = $this->handleResponse($this->sendMessage($message));

        if (isset($response['success']) && $response['success'] === 1) {
            $this->messageQueue->dequeue();
        }

        $this->notifyOnEachSent($message, $response);
    }

    /**
     * @param $message
     *
     * @return mixed
     */
    private function sendMessage(MessageInterface $message)
    {
        $data = $this->createPayload($message);

        $this->stream->write(CURLOPT_POSTFIELDS, json_encode($data));
        $response = null;

        try {
            $response = $this->stream->read();
        } catch (ConnectionException $e) {
            $this->notifyOnError($message, $e);
        }

        return $response;
    }

    /**
     * @param MessageInterface $message
     *
     * @return array
     */
    final protected function createPayload(MessageInterface $message)
    {
        $data = array(
            'registration_ids' => $this->getTokens($message),
            'data'             => array(
                'message' => $message->getMessage(),
                'sound'   => $message->getSound(),
                'badge'   => $message->getBadge(),
            ),
        );
        $customData = $message->getCustomData();
        if (!empty($customData)) {
            $data['data'] = array_merge($data['data'], $customData);
        }

        return $data;
    }

    private function getTokens(MessageInterface $message)
    {
        $ret = array();
        if ($message->isMultiple()) {
            foreach ($message->getToken() as $token) {
                $ret[] = $token;
            }
        } else {
            $ret[] = $message->getToken();
        }

        return $ret;
    }

    public function enqueue(MessageInterface $message)
    {
        if ($message->isMultiple() && count($message->getToken()) > self::MAX_TOKENS) {
            $parts = array_chunk($message->getToken(), self::MAX_TOKENS);
            $message->setToken(null);
            $firstElement = array_shift($parts);

            foreach ($parts as $item) {
                $clonedMessage = clone $message;
                $clonedMessage->setToken($item);
                parent::enqueue($clonedMessage);
            }

            $message->setToken($firstElement);
        }

        parent::enqueue($message);
    }

    protected function getConnectionParams()
    {
        return array(
            CURLOPT_URL            => self::GCM_HOST,
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => $this->headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
        );
    }

    /**
     * @param $response
     *
     * @return mixed
     */
    private function handleResponse($response)
    {
        $decodedResponse = json_decode($response, true);
        if ($decodedResponse === null) {
            return $response;
        }

        return $decodedResponse;
    }
}