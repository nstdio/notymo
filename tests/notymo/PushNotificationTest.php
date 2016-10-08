<?php

use nstdio\notymo\PushNotification;

class PushNotificationTest extends PHPUnit_Framework_TestCase
{

    public function testSkip()
    {
        $pushApnsSkipped = new PushNotification(array(
            'skipApns' => true,
            'gcm' => array('apiKey' => 'api_key')
        ));

        self::assertAttributeCount(1, 'notificationImpl', $pushApnsSkipped);

        $pushGcmSkipped = new PushNotification(array(
            'skipGcm' => true,
            'apns' => array(
                'live' => false,
                'cert' => __DIR__ . '/../scacert.pem',
                'sandboxCert' => __DIR__ . '/../cacert.pem'
            ),
        ));

        self::assertAttributeCount(1, 'notificationImpl', $pushGcmSkipped);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Configuration required for APNSNotification.
     */
    public function testNotProvidedApnsConfig()
    {
        new PushNotification(array(
            'skipGcm' => true,
        ));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Configuration required for GCMNotification.
     */
    public function testNotProvidedGcmConfig()
    {
        new PushNotification(array(
            'skipApns' => true,
        ));
    }
}
