<?php

namespace AppBundle\Form\Operator\Dashboard;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class OperatorType
 * @package AppBundle\Form\Operator\Dashboard
 */
class OperatorType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'email',
                EmailType::class,
                [
                    'label' => 'ایمیل',
                ]
            )
            ->add(
                'fullName',
                null,
                [
                    'label' => 'نام و نام خانوادگی',
                ]
            )
            ->add(
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
            )
            ->add(
                'phone',
                null,
                [
                   'label' => 'شماره تلفن همراه',
                ]
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
            [
                'data_class' => 'AppBundle\Entity\Operator',
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_operator';
    }
}
