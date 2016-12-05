<?php

namespace AppBundle\Form\Customer\Dashboard;

use AppBundle\Form\DataTransformer\DateTimeTransformer;
use Roapp\MediaBundle\Form\RoappImageType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ShipmentType
 * @package AppBundle\Form\Customer\Dashboard
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
            ->add(
                'description',
                null,
                [
                    "label" => "توضیحات",
                    'translation_domain' => 'messages',
                ]
            )
            ->add(
                'value',
                null,
                [
                    "label" => "ارزش بسته",
                    'attr' => ['class' => 'calc_price_item'],
                    'translation_domain' => 'messages',
                ]
            )
            ->add(
                'photoFiles',
                RoappImageType::class
            )
            ->add(
                'pickUpTime',
                TextType::class,
                [
                    "label" => "تعیین زمان تحویل",
                    'translation_domain' => 'messages',
                    'attr' => ['class' => 'js-datepicker'],
                ]
            )
            ->addEventListener(
                FormEvents::PRE_SET_DATA,
                function (FormEvent $event) {
                    $shipment = $event->getData();
                    $form = $event->getForm();
                    if ($shipment->getId() === null) {
                        $form->add(
                            'other',
                            TextType::class,
                            [
                                "label" => "شماره گیرنده",
                                'attr' => ['placeHolder' => 'لطفا شماره تلفن همراه مورد نظر خود را وارد نمایید.', 'class' => 'calc_price_item'],
                                'translation_domain' => 'messages',
                                'mapped' => false,
                            ]
                        );
                    }
                }
            )
        ;
        $builder->get('pickUpTime')
            ->addModelTransformer(new DateTimeTransformer());
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
