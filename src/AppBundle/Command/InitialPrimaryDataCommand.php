<?php

namespace AppBundle\Command;

use AppBundle\DBAL\EnumContractType;
use AppBundle\Entity\Address;
use AppBundle\Entity\BusinessUnit;
use AppBundle\Entity\Customer;
use AppBundle\Entity\Driver;
use AppBundle\Entity\Operator;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class InitialPrimaryDataCommand
 * @package AppBundle\Command
 */
class InitialPrimaryDataCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('app:initial_primary_data:create')
            ->setDescription('fill data base with fake data');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $entityManager = $this->getContainer()->get('doctrine.orm.default_entity_manager');
        $this->createOperator($entityManager);
        $this->createBusinessUnit($entityManager);
        $this->createCustomer($entityManager);
        $this->createDriver($entityManager);
        $this->createAddress($entityManager);

        $output->writeln('Data generated!!!');
    }

    /**
     * @param EntityManager $entityManager
     */
    private function createOperator($entityManager)
    {
        for ($counter = 1; $counter <= 5; $counter++) {
            $operator = new Operator();
            $operator->setEmail('operator'.$counter.'@'.$this->randomEmailName());
            $operator->setFullName('اپراتور'.$counter);
            $operator->setPhone($this->randomPrefixNumber().$this->randomNumber(7));
            $operator->setPassword($this->randomNumber(6));
            $operator->setIsActive(true);

            $entityManager->persist($operator);
            $entityManager->flush($operator);
        }
    }

    /**
     * @param EntityManager $entityManager
     */
    private function createBusinessUnit($entityManager)
    {
        for ($counter = 1; $counter <= 5; $counter++) {
            $businessUnit = new BusinessUnit();
            $businessUnit->setName('واحد شغلی'.$counter);
            $businessUnit->setBusinessType($this->randomBusinessType($entityManager));
            $businessUnit->setContractType(mt_rand(EnumContractType::ENUM_PER_MONTH, EnumContractType::ENUM_PER_SHIPMENT));
            $businessUnit->setAddress('آدرس'.$counter);
            $businessUnit->setPhone('021'.$this->randomNumber(8));

            $entityManager->persist($businessUnit);
            $entityManager->flush($businessUnit);
        }
    }

    /**
     * @param EntityManager $entityManager
     */
    private function createCustomer($entityManager)
    {
        for ($counter = 1; $counter <= 5; $counter++) {
            $customer = new Customer();
            $customer->setFullName('مشتری'.$counter);
            $customer->setPhone($this->randomPrefixNumber().$this->randomNumber(7));
            $customer->setBusinessUnit($this->randomBusinessUnit($entityManager));
            $customer->setPassword($this->randomNumber(6));
            $customer->setEmail('customer'.$counter.'@'.$this->randomEmailName());
            $customer->setIsActive(true);

            $entityManager->persist($customer);
            $entityManager->flush($customer);
        }
    }

    /**
     * @param EntityManager $entityManager
     */
    private function createDriver($entityManager)
    {
        for ($counter = 1; $counter <= 6; $counter++) {
            $driver = new Driver();
            $driver->setFullName('راننده'.$counter);
            $driver->setPhone($this->randomPrefixNumber().$this->randomNumber(7));
            if ($counter % 2 == 0) {
                $driver->setBusinessUnit($this->randomBusinessUnit($entityManager));
            }
            $driver->setPassword($this->randomNumber(6));
            $driver->setStatus(Driver::STATUS_FREE);
            $driver->setIsActive(true);

            $entityManager->persist($driver);
            $entityManager->flush($driver);
        }
    }

    /**
     * @param EntityManager $entityManager
     */
    private function createAddress($entityManager)
    {
        $customers = $entityManager->getRepository("AppBundle:Customer")->findAll();
        foreach ($customers as $customer) {
            $address = new Address();
            $address->setDescription('آدرس _ '.$customer->getFullName());
            $address->setCustomer($customer);
            $address->setCreator($customer);
            $address->setIsPublic(true);
            $address->setLatitude($this->randomFloatNumber(35, 55));
            $address->setLongitude($this->randomFloatNumber(35, 55));
            $address->setBusinessUnit($customer->getBusinessUnit());

            $entityManager->persist($address);
            $entityManager->flush($address);
        }
    }

    /**
     * @param EntityManager $entityManager
     * @return \AppBundle\Entity\BusinessType|null|object
     */
    private function randomBusinessType($entityManager)
    {
        $businessTypeIds = $entityManager
            ->getRepository("AppBundle:BusinessType")
            ->createQueryBuilder('businessType')
            ->select('businessType.id')
            ->getQuery()
            ->getResult();
        $businessTypeId = $businessTypeIds[mt_rand(0, count($businessTypeIds) - 1)]['id'];
        $businessType = $entityManager->getRepository("AppBundle:BusinessType")->find($businessTypeId);

        return $businessType;
    }

    /**
     * @param EntityManager $entityManager
     * @return BusinessUnit|null|object
     */
    private function randomBusinessUnit($entityManager)
    {
        $businessUnitIds = $entityManager
            ->getRepository("AppBundle:BusinessUnit")
            ->createQueryBuilder('businessUnit')
            ->select('businessUnit.id')
            ->getQuery()
            ->getResult();
        $businessUnitId = $businessUnitIds[mt_rand(0, count($businessUnitIds) - 1)]['id'];
        $businessUnit = $entityManager->getRepository("AppBundle:BusinessUnit")->find($businessUnitId);

        return $businessUnit;
    }
    /**
     * @return mixed
     */
    private function randomEmailName()
    {
        $mailNames = array(
            'zoho.com',
            'gmail.com',
            'yahoo.com',
            'hotmail.com',
            'narenjino.ir',
            'mediahamrah.com',
            // and so on

        );

        return $mailNames[mt_rand(0, count($mailNames) -1)];
    }

    /**
     * @return mixed
     */
    private function randomPrefixNumber()
    {
        $prefixNumber = array(
            '0912',
            '0919',
            '0935',
            '0936',
            '0937',
            '0913',
            '0938',
            '0939',
            '0902',
            '0903',
            // and so on

        );

        return $prefixNumber[mt_rand(0, count($prefixNumber) -1)];
    }

    /**
     * @param int $length
     * @return string
     */
    private function randomNumber($length)
    {
        $result = '';

        for ($i = 0; $i < $length; $i++) {
            $result .= mt_rand(1, 9);
        }

        return $result;
    }

    /**
     * @param int $min
     * @param int $max
     * @return int mixed
     */
    private function randomFloatNumber($min, $max)
    {
        return ($min + lcg_value()*(abs($max - $min)));
    }
}
