<?php

namespace AppBundle\Form\Customer\Dashboard;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class RegisterType
 * @package AppBundle\Form\Customer\Dashboard
 */
class RegisterType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'phone',
                TextType::class,
                [
                    "label" => "form.description",
                    'translation_domain' => 'messages',
                ]
            )
            ->add(
                'fullName',
                TextType::class,
                [
                    "label" => "form.value",
                    'translation_domain' => 'messages',
                ]
            )
            ->add(
                'email',
                EmailType::class,
                [
                    "label" => "form.value",
                    'translation_domain' => 'messages',
                ]
            )
            ->add(
                'password',
                RepeatedType::class,
                [
                    'type' => PasswordType::class,
                    'invalid_message' => 'The password fields must match.',
                    'options' => array('attr' => array('class' => 'password-field')),
                    'required' => true,
                    'first_options' => array('label' => 'رمز عبور'),
                    'second_options' => array('label' => 'تکرار رمز عبور'),
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
