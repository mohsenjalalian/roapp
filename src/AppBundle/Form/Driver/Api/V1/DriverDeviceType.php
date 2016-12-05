<?php

namespace AppBundle\Form\Driver\Api\V1;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class DriverDeviceType
 * @package AppBundle\Form
 */
class DriverDeviceType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('deviceUuid')
            ->add('notificationToken')
            ->add('deviceType')
            ->add('latitude')
            ->add('longitude');
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => 'AppBundle\Entity\PersonDevice',
                'csrf_protection' => false,
            ]
        );
    }
}
