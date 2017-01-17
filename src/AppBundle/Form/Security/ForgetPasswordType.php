<?php

namespace AppBundle\Form\Security;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class ForgetPasswordType
 * @package AppBundle\Form\Security
 */
class ForgetPasswordType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'recovery_email',
                EmailType::class,
                [
                    'label' => 'ایمیل',
                    'attr' => ['class' => 'form-control input-lg', 'id' => 'p_email_id', 'placeholder' => 'ایمیل خود را وارد نمایید'],
                    'mapped' => false,
                    'required' => true,
                ]
            )
            ->add(
                'current_route_name',
                HiddenType::class
            );
    }
}
