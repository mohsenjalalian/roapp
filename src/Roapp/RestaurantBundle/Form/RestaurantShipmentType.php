<?php

namespace Roapp\RestaurantBundle\Form;

use AppBundle\Form\Customer\Dashboard\ShipmentType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class RestaurantShipmentType
 * @package Roapp\RestaurantBundle\Form
 */
class RestaurantShipmentType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'roapp_restaurant_bundle_restaurant_shipment_type';
    }

    /**
     * @return mixed
     */
    public function getParent()
    {
        return ShipmentType::class;
    }
}
