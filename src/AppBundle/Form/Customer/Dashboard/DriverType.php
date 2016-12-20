<?php

namespace AppBundle\Form\Customer\Dashboard;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class DriverType
 * @package AppBundle\Form\Customer\Dashboard
 */
class DriverType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fullName', null, [
                'label' => 'نام و نام خانوادگی',
            ])
            ->add('phone', null, [
                'label' => 'شماره تلفن همراه',
            ])
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
                                'required' => false,
                                'first_options'  => array('label' => 'رمز عبور'),
                                'second_options' => array('label' => 'تکرار رمز عبور'),
                            ]
                        );
                    }
                }
            )
            ->add(
                'isActive',
                null,
                [
                    'label' => 'فعال',
                ]
            );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'AppBundle\Entity\Driver',
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_driver';
    }
}
