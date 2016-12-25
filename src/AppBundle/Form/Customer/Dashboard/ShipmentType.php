<?php

namespace AppBundle\Form\Customer\Dashboard;

use AppBundle\Entity\Driver;
use AppBundle\Entity\Shipment;
use AppBundle\Form\DataTransformer\DateTimeTransformer;
use AppBundle\Repository\AddressRepository;
use AppBundle\Repository\DriverRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class ShipmentType
 * @package AppBundle\Form\Customer\Dashboard
 */
class ShipmentType extends AbstractType
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * ShipmentType constructor.
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }
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
        $user = $this->tokenStorage->getToken()->getUser();
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
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($user) {
                $form = $event->getForm();
                $businessUnit = $user->getBusinessUnit();
                $contractType = $businessUnit->getContractType();
                if ($contractType == 1) {
                    $form->add(
                        'isBusinessUnitDriver',
                        CheckboxType::class,
                        [
                            'block_name' => 'isBusinessUnitDriver',
                        ]
                    );
                    $formOptions = [
                      'class'   =>  Driver::class,
                        'query_builder' =>  function (DriverRepository $er) use ($user) {
                            $businessUnit = $user->getBusinessUnit();

                            return $er->businessUnitDriver($businessUnit, Driver::STATUS_FREE);
                        },
                        'mapped'    =>  false,
                        'expanded' => true,
                        'multiple'  => false,
                        'required'  =>  false,
                    ];
                    $form->add('driver', EntityType::class, $formOptions);
                }
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
