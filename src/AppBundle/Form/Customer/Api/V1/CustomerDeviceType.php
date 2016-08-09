<?php

namespace AppBundle\Form\Customer\Api\V1;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class CustomerDeviceType
 * @package AppBundle\Form
 */
class CustomerDeviceType extends AbstractType
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
            ->add('longitude')
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\CustomerDevice',
            'csrf_protection' => false,
        ));
    }
}
