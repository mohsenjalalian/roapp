<?php

namespace Roapp\RestaurantBundle\Form;

use AppBundle\Form\Operator\Dashboard\BusinessUnitType;
use AppBundle\Repository\AddressRepository;
use Doctrine\ORM\EntityRepository;
use Roapp\RestaurantBundle\Entity\Restaurant;
use Roapp\RestaurantBundle\Repository\RestaurantRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class RestaurantType
 * @package Roapp\RestaurantBundle\Form
 */
class RestaurantType extends AbstractType
{
    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array                                        $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            /** @var Restaurant $restaurant */
            $restaurant = $event->getData();
            $event->getForm()->add('defaultAddress', EntityType::class, [
                'class' => 'AppBundle\Entity\Address',
                'query_builder' => function (AddressRepository $er) use ($restaurant) {
                    return $er->createQueryBuilder('address')
                        ->where('address.businessUnit = :businessUnit')
                        ->setParameter('businessUnit', $restaurant);
                },
                'choice_label' => 'description',
            ]);
        });
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Restaurant::class,
            ]
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'roapp_restaurant_bundle_restaurant_type';
    }

    /**
     * @return null|string
     */
    public function getParent()
    {
        return BusinessUnitType::class;
    }
}
