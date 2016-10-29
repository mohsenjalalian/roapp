<?php

namespace AppBundle\Utils\Notification\Messages;
use AppBundle\Entity\Person;
use Doctrine\ORM\EntityManager;

/**
 * Class SingleMessage
 * 
 * @package AppBundle\Utils\Notification\Messages
 */
class SingleMessage extends AbstractMessage
{
    /**
     * @var Person
     */
    private $person;

    /**
     * SingleMessage constructor.
     *
     * @param Person        $person Person to send notification
     * @param EntityManager $entityManager
     */
    public function __construct($person, $entityManager)
    {
        parent::__construct($entityManager);
        $this->person = $person;
    }

    /**
     * @inheritdoc
     */
    public function getParameters()
    {
        $personActiveDevice = $this->entityManager
            ->getRepository('AppBundle:PersonDevice')
            ->getPersonActiveDevice($this->person);
        $notificationToken = $personActiveDevice->getNotificationToken();
        $keysArray = [
            'to' => $notificationToken,
            'notification' => $this->createNotificationArray()
        ];
        
        return $keysArray;
    }
}