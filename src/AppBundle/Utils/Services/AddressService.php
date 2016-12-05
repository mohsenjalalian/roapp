<?php
/**
 * User: mohsenjalalian
 * Date: 10/18/16
 * Time: 10:39 AM
 */
namespace AppBundle\Utils\Services;

use Doctrine\ORM\EntityManager;
use AppBundle\Entity\Address;
use AppBundle\Entity\Customer;

/**
 * Class AddressService
 * @package AppBundle\Utils\Services
 */
class AddressService
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * Address constructor.
     *
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param Address $address
     * @param object  $owner
     * @param object  $user
     * @return Address
     */
    public function createAddress(Address $address, $owner, $user)
    {
        $em = $this->entityManager;

        $address->setCreator($user);

        if ($owner == null) {
            $address->setCustomer($user);
        } else {
            $customer = $this->entityManager
                ->getRepository('AppBundle:Customer')
                ->findOneBy(
                    [
                        'phone' => $owner,
                    ]
                );

            if (!$customer) {
                $customer = new Customer();
                $customer->setPhone($owner);
                $customer->setPassword('1234');
                $em->persist($customer);
            }
            $address->setIsPublic(false);
            $address->setCustomer($customer);
        }

        $em->persist($address);
        $em->flush();

        return $address;
    }
}
