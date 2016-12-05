<?php

namespace AppBundle\Form\Operator\Dashboard;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ShipmentType
 * @package AppBundle\Form\Operator\Dashboard
 */
class ShipmentType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('description')
            ->add('value')
            ->add('status')
            ->add('pickUpTime', DateTimeType::class)
            ->add('createdAt', DateTimeType::class)
            ->add('price')
            ->add('type')
            ->add('ownerAddress');
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => 'AppBundle\Entity\Shipment',
            ]
        );
    }
}
