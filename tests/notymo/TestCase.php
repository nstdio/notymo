<?php
namespace nstdio\tests\notymo;
use nstdio\notymo\Connection;

/**
 * Class TestCase
 *
 * @author Edgar Asatryan <nstdio@gmail.com>
 */
class TestCase extends \PHPUnit_Framework_TestCase
{
    protected $connectionClassName = 'nstdio\notymo\CurlWrapper';

    /**
     * @param $readReturn
     *
     * @return Connection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function mockConnection($readReturn = array())
    {
        $mockConnection = $this->getMockBuilder($this->connectionClassName)
            ->setMethods(array("read"))
            ->getMock();

        $mockConnection->expects($this->any())
            ->method("read")
            ->willReturn(json_encode($readReturn));

        return $mockConnection;
    }

    protected function mockMessage($tokens, $customData = array())
    {
        $message = $this->getMockBuilder('nstdio\notymo\Message')
            ->setMethods(array("getToken", "isMultiple", "getType", "getCustomData"))
            ->getMock();

        $message->expects($this->any())
            ->method("getToken")
            ->willReturn($tokens);

        $message->expects($this->any())
            ->method("isMultiple")
            ->willReturn(is_array($tokens));

        $message->expects($this->any())
            ->method("getCustomData")
            ->willReturn($customData);

        return $message;
    }
}