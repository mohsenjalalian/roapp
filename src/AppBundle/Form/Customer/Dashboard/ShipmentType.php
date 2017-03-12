<?php

namespace AppBundle\Form\Customer\Dashboard;

use AppBundle\Entity\Driver;
use AppBundle\Entity\Shipment;
use AppBundle\Form\DataTransformer\TimeTransformer;
use AppBundle\Repository\AddressRepository;
use AppBundle\Repository\DriverRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\Constraints\Choice;

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
     * @var RequestStack
     */
    private $requestStack;

    /**
     * ShipmentType constructor.
     * @param TokenStorageInterface $tokenStorage
     * @param RequestStack          $requestStack
     */
    public function __construct(TokenStorageInterface $tokenStorage, RequestStack $requestStack)
    {
        $this->tokenStorage = $tokenStorage;
        $this->requestStack = $requestStack;
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
                ChoiceType::class,
                [
                    "label" => "تعیین زمان تحویل",
                    'attr' => ['class' => 'duration'],
                    'translation_domain' => 'messages',
                    'choices' => $this->durations(),
                    'choice_label' => function ($key, $index) {
                        return $index;
                    },
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
                            'autocomplete' => 'off',
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
        $user = $this->tokenStorage->getToken()->getUser();
        $builder->get('pickUpTime')
            ->addModelTransformer(new TimeTransformer());

        $formModifier = function (FormInterface $form, $phone) {
            $form->add('otherAddress', EntityType::class, [
                'attr'      => ['class' => 'select-address'],
                'label'     =>  'آدرس گیرنده',
                'class'       => 'AppBundle:Address',
                'placeholder' => '',
                'expanded' => true,
                'multiple' => false,
                'query_builder' => function (AddressRepository $addressRepository) use ($phone) {
                    $user = $this->tokenStorage->getToken()->getUser();

                    return $addressRepository->createQueryBuilder('address')
                        ->join('address.customer', 'customer')
                        ->where('customer.phone = :phone and address.isPublic = :public')
                        ->orWhere('customer.phone = :phone and address.creator = :user')
                        ->setParameter('phone', $phone)
                        ->setParameter('public', true)
                        ->setParameter('user', $user);
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
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($user, $builder) {
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

                            return $er->businessUnitDriver($businessUnit, Driver::STATUS_FREE, $this->requestStack->getCurrentRequest()->get('driverId'));
                        },
                        'mapped'    =>  false,
                        'expanded' => true,
                        'multiple'  => false,
                        'required'  =>  false,
                    ];
                    $form->add('driver', EntityType::class, $formOptions);

                    $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) use ($user) {
                        $form = $event->getForm();

                        $formOptions = [
                            'class'   =>  Driver::class,
                            'query_builder' =>  function (DriverRepository $er) use ($user) {
                                $businessUnit = $user->getBusinessUnit();

                                return $er->businessUnitDriver($businessUnit, Driver::STATUS_FREE, $this->requestStack->getCurrentRequest()->get('driverId'));
                            },
                            'mapped'    =>  false,
                            'expanded' => true,
                            'multiple'  => false,
                            'required'  =>  false,
                        ];

                        $form->add('driver', EntityType::class, $formOptions);
                    });
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

    /**
     * @return array
     */
    public function durations()
    {
        $times = [];
        $x = 30;
        while ($x <= 720) {
            if ($x >= 60) {
                $h = $x / 60;
                $times[$h.'h'] = $x;
            } else {
                $times[$x.'min'] = $x;
            }
            $x = $x + 30;
        }

        return $times;
    }
}
