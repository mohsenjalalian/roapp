<?php
/**
 * User: mohsenjalalian
 * Date: 11/22/16
 * Time: 10:00 AM
 */
namespace AppBundle\EventListener;

use AppBundle\Entity\Shipment;
use Symfony\Component\DependencyInjection\Container;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use r;

/**
 * Class ShipmentListener
 * @package AppBundle\EventListener
 */
class ShipmentListener
{
    /**
     * @var \Symfony\Component\DependencyInjection\Container
     */
    private $container;

    /**
     * ShipmentListener constructor.
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @param PreUpdateEventArgs $eventArgs
     */
    public function preUpdate(PreUpdateEventArgs $eventArgs)
    {
        if ($eventArgs->getEntity() instanceof Shipment) {
            if ($eventArgs->hasChangedField('status')) {
                if (in_array($eventArgs->getEntity()->getStatus(), Shipment::TRACK_ENABLED_STATUSES)) {
                    $conn = r\connect('localhost', '28015', 'roapp', $this->container->getParameter('rethink_password'));
                    r\table('shipment')
                        ->filter(
                            [
                                'shipment_id' => $eventArgs->getEntity()->getId(),
                            ]
                        )
                        ->update(['status' => "enabled"])
                        ->run($conn);
                } elseif (in_array($eventArgs->getEntity()->getStatus(), Shipment::TRACK_DISABLED_STATUSES)) {
                    $conn = r\connect('localhost', '28015', 'roapp', $this->container->getParameter('rethink_password'));
                    r\table('shipment')
                        ->filter(
                            [
                                'shipment_id' => $eventArgs->getEntity()->getId(),
                            ]
                        )
                        ->update(['status' => "disabled"])
                        ->run($conn);
                }
            }
        }
    }
}
