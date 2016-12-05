<?php

namespace AppBundle\Utils\factories;

use AppBundle\Entity\Person;
use AppBundle\Utils\Notification\Messages\BroadcastMessage;
use AppBundle\Utils\Notification\Messages\MulticastMessage;
use AppBundle\Utils\Notification\Messages\SingleMessage;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;

/**
 * Class NotificationMessageFactory
 *
 * @package AppBundle\Utils\factories
 */
class NotificationMessageFactory
{
    /**
     * @var EntityManager $entityManager
     */
    private $entityManager;

    /**
     * NotificationMessageFactory constructor.
     *
     * @param EntityManager $entityManager
     */
    public function __construct($entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param mixed $param
     *
     * @return mixed
     */
    public function create($param)
    {
        if ($param instanceof Person) {
            return new SingleMessage($param, $this->entityManager);
        } elseif ($param instanceof ArrayCollection) {
            return new MulticastMessage($param, $this->entityManager);
        } elseif (is_string($param)) {
            return new BroadcastMessage($param);
        }
    }
}
