<?php

namespace AppBundle\Utils\Notification\Messages;
use Doctrine\ORM\EntityManager;

/**
 * Class BulkMessage
 * 
 * @package AppBundle\Utils\Notification\Messages
 */
class BroadcastMessage extends AbstractMessage
{
    /**
     * @var string $topic
     */
    private $topic;

    /**
     * BulkMessage constructor.
     *
     * @param string $topic Topic to broadcast
     */
    public function __construct($topic)
    {
        $this->topic = $topic;
    }

    /**
     * @inheritdoc
     */
    public function getParameters()
    {
        $keysArray = [
            'to' => $this->topic,
            'notification' => $this->createNotificationArray()
        ];

        return $keysArray;
    }
}