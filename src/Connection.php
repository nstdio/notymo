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
    public function open(array $params, $address);

    public function write($option, $data);

    public function read();

    public function close();
}