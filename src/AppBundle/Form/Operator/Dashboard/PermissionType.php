<?php

namespace AppBundle\Form\Operator\Dashboard;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class PermissionType
 * @package AppBundle\Form\Operator\Dashboard
 */
class PermissionType extends AbstractType
{
    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array                                        $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('roles', EntityType::class, [
                'class' => 'AppBundle:Role',
                'expanded' => true,
                'multiple' => true,
            ])
        ;
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Permission',
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'permission_type';
    }
}
