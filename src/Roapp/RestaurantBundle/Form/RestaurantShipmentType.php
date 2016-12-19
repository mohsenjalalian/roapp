<?php

namespace Roapp\RestaurantBundle\Form;

use AppBundle\Form\Customer\Dashboard\ShipmentType;
use Roapp\RestaurantBundle\Entity\RestaurantShipment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class RestaurantShipmentType
 * @package Roapp\RestaurantBundle\Form
 */
class RestaurantShipmentType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('test');
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => RestaurantShipment::class,
                'template' => 'RoappRestaurantBundle:Shipment:_shipment_form.html.twig',
                'javascript' => 'bundles/roapprestaurant/javascript/form.js',
                'stylesheet' => 'bundles/roapprestaurant/stylesheet/form.css',
            ]
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'restaurant_shipment_type';
    }

    /**
     * @return mixed
     */
    public function getParent()
    {
        return ShipmentType::class;
    }
}
