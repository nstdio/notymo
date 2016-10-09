<?php
namespace nstdio\notymo;

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

    public function open(array $params, $socketAddress)
    {
        $this->stream = curl_init($socketAddress);

        curl_setopt_array($this->stream, $params);

        return $this;
    }

    public function write($option, $string)
    {
        curl_setopt($this->stream, $option, $string);
    }

    public function close()
    {
        if (is_resource($this->stream)) {
            curl_close($this->stream);
        }
    }

    public function read()
    {
        $response = curl_exec($this->stream);

        return $response;
    }
}