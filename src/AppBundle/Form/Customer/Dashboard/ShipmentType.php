<?php

namespace AppBundle\Form\Customer\Dashboard;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ShipmentType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('description', null,
                array(
                    "label"=>"form.description",
                    'translation_domain' => 'messages'
                ))
            ->add('value', null,
                array(
                    "label"=>"form.value",
                    'translation_domain' => 'messages'
                ))
//            ->add('status')
            ->add('pickUpTime', DateTimeType::class,
                array(
                    "label"=>"form.pickUpTime",
                    'translation_domain' => 'messages',
                    'widget' => 'single_text',
                    'html5' => false,
                    'attr' => ['class' => 'js-datepicker']
                ))
//            ->add('createdAt', DateTimeType::class)
//            ->add('price')
//            ->add('type')
            ->add('ownerAddress', null,
                array(
                    "label"=>"form.ownerAddress",
                    'translation_domain' => 'messages'
                ))
            ->add('otherAddress', null,
                array(
                    "label"=>"other.address",
                    'translation_domain' => 'messages',
                    'mapped' => false
                ))
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Shipment'
        ));
    }
}
