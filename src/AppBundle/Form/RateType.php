<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class RateType
 * @package AppBundle\Form
 */
class RateType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'description',
                TextareaType::class,
                [
                    'label' => 'توضیحات',
                ]
            )
            ->add(
                'point',
                ChoiceType::class,
                [
                    'choices' =>
                        [
                            '1' => '1',
                            '2' => '2',
                            '3' => '3',
                            '4' => '4',
                            '5' => '5',
                        ],
                    'label' => 'امتیاز از 5 :',
                    'expanded' => true,
                    'multiple' => false,
                ]
            )
            ->add(
                'save',
                SubmitType::class,
                [
                    'label' => 'ثبت امتیاز',
                ]
            )
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Rate',
        ));
    }
}
