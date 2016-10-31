<?php

namespace AppBundle\Form\Customer\Dashboard;

use AppBundle\Form\DataTransformer\DateTimeTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegisterType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('phone', TextType::class,
                array(
                    "label"=>"form.description",
                    'translation_domain' => 'messages',
                )
            )
            ->add('fullName', TextType::class,
                array(
                    "label"=>"form.value",
                    'translation_domain' => 'messages'
                )
            )
            ->add('email', EmailType::class,
                array(
                    "label"=>"form.value",
                    'translation_domain' => 'messages'
                )
            )
            ->add('password', RepeatedType::class,
                array(
                    'type' => PasswordType::class,
                    'invalid_message' => 'The password fields must match.',
                    'options' => array('attr' => array('class' => 'password-field')),
                    'required' => true,
                    'first_options'  => array('label' => 'رمز عبور'),
                    'second_options' => array('label' => 'تکرار رمز عبور'),
                )
            )
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Customer'
        ));
    }
}
