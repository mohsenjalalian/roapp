<?php
/**
 * Created by PhpStorm.
 * User: mohsenjalalian
 * Date: 10/18/16
 * Time: 10:39 AM
 */
namespace AppBundle\Utils;

use Doctrine\ORM\EntityManager;
use AppBundle\Entity\Address;
use AppBundle\Entity\Customer;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AddressUtils
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * Address constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager, ValidatorInterface $validator)
    {
        $this->entityManager = $entityManager;
        $this->validator = $validator;

    }

    public function newAddress(Address $address, $owner, $user)
    {
        $validator = $this->validator;
        $em = $this->entityManager;

        $address->setCreator($user);

        if ($owner == null) {
            $address->setCustomer($user);
        } else {
            $customer = $this->entityManager
                ->getRepository('AppBundle:Customer')
                ->findOneBy(
                    array('phone' => $owner)
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

        $errors = $validator->validate($address);

        if (count($errors) > 0) {

            $errorsString = (string) $errors;

            return $errorsString;
        }

        $em->persist($address);
        $em->flush();

        return true;



    }
}