<?php

namespace AppBundle\Form\Operator\Dashboard;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
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
            ->add(
                'password',
                PasswordType::class,
                [
                    'label' => 'رمز عبور',
                ]
            )
            ->add(
                'isActive',
                null,
                [
                    'label' => 'وضعیت',
                ]
            );
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
