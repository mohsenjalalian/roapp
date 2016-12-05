<?php

namespace AppBundle\Form\Driver\Api\V1;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class VerificationReportType
 * @package AppBundle\Form\Driver\Api\V1
 */
class VerificationReportType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('deviceUuid')
            ->add('deviceType')
            ->add('verificationCode')
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'csrf_protection' => false,
            ]
        );
    }
}
