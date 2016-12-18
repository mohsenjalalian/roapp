<?php

namespace Roapp\RestaurantBundle\Form;

use AppBundle\Form\Customer\Dashboard\ShipmentType;
use Roapp\RestaurantBundle\Entity\RestaurantShipment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RestaurantShipmentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('test');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => RestaurantShipment::class,
                'template' => 'RoappRestaurantBundle:Shipment:_shipment_form.html.twig'
            ]
        );
    }

    public function getName()
    {
        return 'restaurant_shipment_type';
    }

    public function getParent() {
        return ShipmentType::class;
    }
}
