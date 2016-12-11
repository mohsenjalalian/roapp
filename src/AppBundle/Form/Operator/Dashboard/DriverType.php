<?php

namespace AppBundle\Form\Operator\Dashboard;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

/**
 * Class DriverType
 * @package AppBundle\Form\Operator\Dashboard
 */
class DriverType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fullName', null, [
                'label' => 'نام و نام خانوادگی',
            ])
            ->addEventListener(
                FormEvents::PRE_SET_DATA,
                function (FormEvent $event) {
                    $driver = $event->getData();
                    $form = $event->getForm();
                    if ($driver->getId() !== null) {
                        $form->add(
                            'currentPassword',
                            PasswordType::class,
                            [
                                'label' => 'رمز فعلی',
                                'required' => false,
                            ]
                        );
                        $form->add(
                            'newPassword',
                            RepeatedType::class,
                            [
                                'type' => PasswordType::class,
                                'invalid_message' => 'رمز های عبور وارد شده باید یکسان باشند',
                                'options' => array('attr' => array('class' => 'password-field')),
                                'required' => false,
                                'first_options'  => array('label' => 'رمز عبور'),
                                'second_options' => array('label' => 'تکرار رمز عبور'),
                            ]
                        );
                    }
                }
            )
            ->addEventListener(
                FormEvents::PRE_SET_DATA,
                function (FormEvent $event) {
                    $driver = $event->getData();
                    $form = $event->getForm();
                    if ($driver->getId() === null) {
                        $form->add(
                            'password',
                            RepeatedType::class,
                            [
                                'type' => PasswordType::class,
                                'invalid_message' => 'رمز های عبور وارد شده باید یکسان باشند',
                                'options' => array('attr' => array('class' => 'password-field')),
                                'required' => true,
                                'first_options'  => array('label' => 'رمز عبور'),
                                'second_options' => array('label' => 'تکرار رمز عبور'),
                            ]
                        );
                    }
                }
            )
//            ->addEventListener(
//                FormEvents::PRE_SET_DATA,
//                function (FormEvent $event) {
//                    $driver = $event->getData();
//                    $form = $event->getForm();
//                    if ($driver->getId() !== null) {
//                        $form->add(
//                            'newPassword',
//                            RepeatedType::class,
//                            [
//                                'type' => PasswordType::class,
//                                'invalid_message' => 'رمز های عبور وارد شده باید یکسان باشند',
//                                'options' => array('attr' => array('class' => 'password-field')),
//                                'required' => false,
//                                'first_options'  => array('label' => 'رمز عبور'),
//                                'second_options' => array('label' => 'تکرار رمز عبور'),
//                            ]
//                        );
//                    }
//                }
//            )
            ->add(
                'isActive',
                null,
                [
                    'label' => 'فعال',
                ]
            )
            ->add('phone', null, [
                'label' => 'شماره تلفن همراه',
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => 'AppBundle\Entity\Driver',
            ]
        );
    }
}
