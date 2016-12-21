<?php

namespace AppBundle\Form\Customer\Dashboard;

use AppBundle\Entity\Shipment;
use AppBundle\Form\DataTransformer\DateTimeTransformer;
use AppBundle\Repository\AddressRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

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
                'pickUpTime',
                TextType::class,
                [
                    "label" => "تعیین زمان تحویل",
                    'translation_domain' => 'messages',
                    'attr' => ['class' => 'js-datepicker'],
                ]
            )
            ->add(
                'other_phone',
                TextType::class,
                [
                    "label" => "شماره گیرنده",
                    'attr' =>
                        [
                            'placeHolder' => 'لطفا شماره تلفن همراه مورد نظر خود را وارد نمایید.',
                            'class' => 'calc_price_item other-phone',
                        ],
                    'translation_domain' => 'messages',
                ]
            )
            ->add(
                'ownerAddress',
                EntityHiddenType::class,
                [
                    'class' => 'AppBundle\Entity\Address',
                    'attr'  =>
                    [
                        'class' =>  'owner-address',
                    ],
                ]
            )
            ->add(
                'selected_driver',
                HiddenType::class,
                [
                    'mapped' => false,
                    'attr'  =>
                    [
                      'class'   =>  'selected-driver',
                    ],
                ]
            )
        ;
        $builder->get('pickUpTime')
            ->addModelTransformer(new DateTimeTransformer());

        $formModifier = function (FormInterface $form, $phone) {
            $form->add('otherAddress', EntityType::class, [
                'attr'      => ['class' => 'select-address'],
                'label'     =>  'آدرس گیرنده',
                'class'       => 'AppBundle:Address',
                'placeholder' => '',
                'expanded' => true,
                'multiple' => false,
                'query_builder' => function (AddressRepository $addressRepository) use ($phone) {
                    return $addressRepository->createQueryBuilder('address')
                        ->join('address.customer', 'customer')
                        ->where('customer.phone = :phone')->setParameter('phone', $phone)
                        ->andWhere('address.isPublic = :public or address.creator = customer')
                        ->setParameter('public', true);
                },
            ]);
        };

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formModifier) {
                /** @var Shipment $data */
                $data = $event->getData();

                $formModifier($event->getForm(), $data->getOtherPhone());
            }
        );

        $builder->get('other_phone')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($formModifier) {
                $otherPhone = $event->getForm()->getData();

                $formModifier($event->getForm()->getParent(), $otherPhone);
            }
        );
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
