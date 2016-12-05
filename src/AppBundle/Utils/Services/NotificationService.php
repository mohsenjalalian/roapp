<?php

namespace AppBundle\Utils\Services;

use Doctrine\ORM\EntityManager;

/**
 * Class SendNotification
 *
 * @package AppBundle\Utils\Services
 */
class NotificationService
{
    const URL = 'https://fcm.googleapis.com/fcm/send';
    const HEADERS = [
        'Authorization:key = AIzaSyBJaQ9dbnGXZbWoNu70nibNsUajGUj2GpA',
        'Content-Type: application/json',
    ];

    /**
     * @var EntityManager $entityManager
     */
    private $entityManager;

    /**
     * @var array $firebaseConfig
     */
    private $firebaseConfig;

    /**
     * SendNotification constructor.
     *
     * @param EntityManager $entityManager
     * @param array         $firebaseConfig
     */
    public function __construct($entityManager, $firebaseConfig)
    {
        $this->entityManager = $entityManager;
        $this->firebaseConfig = $firebaseConfig;
    }

    /**
     * @param array $data
     * @deprecated
     * @return bool
     */
    public function sendNotification($data)
    {
        $sendUrl = 'https://fcm.googleapis.com/fcm/send';
        $headers = array(
            'Authorization:key = AIzaSyBJaQ9dbnGXZbWoNu70nibNsUajGUj2GpA',
            'Content-Type: application/json',
        );
        $title = $data['title'];
        $text = $data['body'];
        $topic = $data['topic'];
        $parameters = serialize($data['parameters']);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $sendUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $fields = array(
            'notification' => array(
                'title' => $title,
                'body' => $text,
                'tag' => $parameters,
            ),
            'to' => '/topics/'.$topic,
        );
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        if ($result === false) {
            die("curl deny:".curl_error($ch));
        }
        curl_close($ch);
        // send request to driver

        return true;
    }

    /**
     * Sends notification message
     *
     * @param array $keysArray Keys for building notification messages
     *
     * @return bool
     */
    public function send($keysArray)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::URL);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            [
                'Authorization:key = '.$this->firebaseConfig['authorization_key'],
                'Content-Type: application/json',
            ]
        );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($keysArray));
        $result = curl_exec($ch);
        curl_close($ch);

        if ($result == false) {
            return false;
        }

        return true;
    }
}
