<?php

namespace AppBundle\Form\Operator\Dashboard;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class CustomerType
 * @package AppBundle\Form\Operator\Dashboard
 */
class CustomerType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'fullName',
                null,
                [
                    'label' => 'نام و نام خانوادگی',
                ]
            )
            ->add(
                'email',
                EmailType::class,
                [
                    'label' => 'ایمیل',
                ]
            )
            ->add(
                'phone',
                null,
                [
                    'label' => 'شماره تلفن همراه',
                ]
            )
            ->addEventListener(
                FormEvents::PRE_SET_DATA,
                function (FormEvent $event) {
                    $customer = $event->getData();
                    $form = $event->getForm();
                    if ($customer->getId() !== null) {
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
                    $customer = $event->getData();
                    $form = $event->getForm();
                    if ($customer->getId() === null) {
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
            ->add(
                'isActive',
                null,
                [
                    'label' => 'وضعیت',
                ]
            )
            ->add(
                'businessUnit',
                null,
                [
                    'label' => 'واحد شغلی',
                ]
            )
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => 'AppBundle\Entity\Customer',
            ]
        );
    }
}
