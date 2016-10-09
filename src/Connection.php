<?php
namespace nstdio\notymo;

/**
 * Interface Connection
 *
 * @package nstdio\notymo
 * @author  Edgar Asatryan <nstdio@gmail.com>
 */
interface Connection
{
    /**
     * @param array  $params
     * @param string $address
     */
    public function open(array $params, $address);

    /**
     * @param $option
     * @param $data
     *
     * @return mixed
     */
    public function write($option, $data);

    /**
     * @return mixed
     */
    public function read();

    /**
     * @return mixed
     */
    public function close();
}