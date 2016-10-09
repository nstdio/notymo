# notymo [![Build Status](https://travis-ci.org/nstdio/notymo.svg?branch=master)](https://travis-ci.org/nstdio/notymo) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/nstdio/notymo/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/nstdio/notymo/?branch=master) [![Code Coverage](https://scrutinizer-ci.com/g/nstdio/notymo/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/nstdio/notymo/?branch=master)

The **notymo** is a library  which can help you to send push notifications on iOS and Andriod devices using single interface. The Library has no external dependencies.

# Installation

The suggested installation method is via [composer](https://getcomposer.org/):
```
$ composer require nstdio/notymo: "dev-master"
```
or add
```
"nstdio/notymo": "dev-master"
```
to the `require` section of your `composer.json` file.

# Usage

## Single Interface
```php
use nstdio\notymo\Message;
use nstdio\notymo\PushNotification;

$push = new PushNotification(array(
         // If you dоn't want to use one of the services we can just skip them loading.
         // It's obvious that the skipped service is not necessary to configure.
         // 'skipApns' => true,
         // 'skipGcm'  => true,
        'apns' => array(
            'live' => true, // We need to connect to APNS production server
            'cert' => 'live_cert.pem' // Also we must specify a SSL certificate for sending notification to iOS devices.
        ),
        'gcm'  => array(
            'apiKey' => 'api_key' // Google GCM Service API key. 
        ),
    )
);

/**
 * If we have multiple recipients and all of them should receive same data we can create 
 * one single instance of Message class and send messages at once.
 */
$msg = new Message();
$msg->setType(Message::TYPE_ANDROID);
$msg->setMessage("You have a notification.");
$msg->setSound("default");
$msg->setBadge(2);
$msg->setCustomData(array("user_data" => array()));
$msg->setToken(range(0, 10000));

/**
 * Just clone original message and replace old device's tokens with new once for iOS devices.
 */
$msg2 = clone $msg;
$msg2->setToken(range(10000, 20000));
$msg2->setType(Message::TYPE_IOS);


$push->enqueue($msg);
$push->enqueue($msg2); // Adding messages to queue

$push->send(); // Send notifications.
```
## iOS

```php
use nstdio\notymo\APNSNotification;
use nstdio\notymo\Message;

$apns = new APNSNotification(true, 'live_cert.pem');

$msg = new Message(Message::TYPE_IOS); // We can pass message type to constructor.
$msg->setMessage("This notification sent by cron.");
$msg->setSound("bang_bang");
$msg->setCustomData(array("segue" => "toSignInView"));
$msg->setToken(range(0, 10000)); //

$apns->enqueue($msg); // Adding messages to queue

$apns->send(); // Send notifications.
```

## Android

```php
use nstdio\notymo\GCMNotification;
use nstdio\notymo\Message;

$gcm = new GCMNotification("gcm_api_key");

$msg = new Message(Message::TYPE_ANDROID);
// ... same story as in iOS example.
$msg->setToken(range('A', 'Z'));

$gcm->enqueue($msg);

$gcm->send();
```


## Applying lifecycle callbacks

### [LifeCycleCallback](https://github.com/nstdio/notymo/blob/master/src/LifeCycleCallback.php "interface LifeCycleCallback")

| Method       | Comment | Callback signature |
| -------------| ------- | ------------------ |
| `void onComplete(Closure $callback)` | Will be called when all messages are sent. | `void function(MessageQueue $messages)` |
| `void onEachSent(Closure $callback)` | Will be called when the every message was sent. | `void function(MessageInterface $message, array $response)` |
| `void onError(Closure $callback)`    | Will be called when error occurs. Note that when error occured and this callback is not defined, an exception will be thrown. | `void function(MessageInterface $message, PushNotificationException $exc)` |
| `void detach()`     | This method has no `Closure` argument because it is not involved in the message sending lifecycle. The single assignment of this method to remove callbacks. Will be called immediately after `onSent`.| - |

```php
// ...

$push->onComplete(function(MessageQueue $queue) {
    /** @var MessageInterface $message */
    foreach ($queue as $message) {
        printf("Message %s not sent\n", $message->getToken())
    }
});

$push->onSent(function(MessageInterface $message, $response) use ($model) {
    $model->save(array(
        'device_token' => $message->getToken(),
        'is_sent' => $response['success'],
    ));
});

$push->onError(function(MessageInterface $message, PushNotificationException $e) {
    printf("Error %s occurs while sending %s\n", $message->getToken(), $e->getMessage());
});

$push->send();
```