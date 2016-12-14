<?php

namespace AppBundle\Form\Operator\Dashboard;

use AppBundle\DBAL\EnumContractType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class CustomerType
 * @package AppBundle\Form\Operator\Dashboard
 */
class BusinessUnitType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('businessType')
            ->add('contractType', ChoiceType::class, [
                'choices' => array_combine(array_values(EnumContractType::getValues()), array_keys(EnumContractType::getValues())),
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
            'data_class' => 'AppBundle\Entity\BusinessUnit',
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_businessunit';
    }
}
