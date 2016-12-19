<?php

namespace AppBundle\Form\Customer\Dashboard;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class CustomerType
 * @package AppBundle\Form\Customer\Dashboard
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
            ->add(
                'currentPassword',
                PasswordType::class,
                [
                    'label' => 'رمز فعلی',
                    'required' => false,
                ]
            )
            ->add(
                'newPassword',
                RepeatedType::class,
                [
                    'type' => PasswordType::class,
                    'invalid_message' => 'رمز های عبور وارد شده باید یکسان باشند',
                    'options' => array('attr' => array('class' => 'password-field')),
                    'required' => false,
                    'first_options'  => array('label' => 'رمز عبور جدید'),
                    'second_options' => array('label' => 'تکرار رمز عبور جدید'),
                ]
            )
        ;
    }
}
