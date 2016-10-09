<?php
namespace nstdio\notymo;

use nstdio\notymo\exception\ConnectionException;

/**
 * Class CurlWrapper
 *
 * @package nstdio\notymo
 * @author  Edgar Asatryan <nstdio@gmail.com>
 */
class CurlWrapper implements Connection
{
    /**
     * @var resource
     */
    private $stream;

    /**
     * @inheritdoc
     */
    public function open(array $params, $socketAddress)
    {
        $this->stream = curl_init($socketAddress);

        curl_setopt_array($this->stream, $params);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function write($option, $string)
    {
        curl_setopt($this->stream, $option, $string);
    }

    /**
     * @inheritdoc
     */
    public function close()
    {
        if (is_resource($this->stream)) {
            curl_close($this->stream);
        }
    }

    /**
     * @inheritdoc
     */
    public function read()
    {
        $response = curl_exec($this->stream);
        if ($response === false) {
            throw new ConnectionException(curl_error($this->stream), curl_errno($this->stream));
        }
        return $response;
    }
}