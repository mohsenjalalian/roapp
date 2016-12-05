<?php

namespace AppBundle\Utils\Notification\Messages;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;

/**
 * Class MulticastMessage
 *
 * @package AppBundle\Utils\Notification\Messages
 */
class MulticastMessage extends AbstractMessage
{
    /**
     * @var ArrayCollection $people
     */
    private $people;

    /**
     * MulticastMessage constructor.
     *
     * @param ArrayCollection $people        Person collection to send message
     * @param EntityManager   $entityManager
     */
    public function __construct($people, $entityManager)
    {
        parent::__construct($entityManager);
        $this->people = $people;
    }

    /**
     * @inheritdoc
     * @return array
     */
    public function getParameters()
    {
        $notificationTokens = null;
        while (($person = $this->people->next()) !== false) {
            $notificationTokens[] = $person->getNotificationToken();
        }
        $keysArray = [
            'registration_ids' => $notificationTokens,
            'notification' => $this->createNotificationArray(),
        ];

        return $keysArray;
    }
}
