<?php

namespace AppBundle\Form\Customer\Dashboard;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class PaymentStyle
 * @package AppBundle\Form\Customer\Dashboard
 */
class PaymentStyle extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'method',
                ChoiceType::class,
                [
                    'choices' => [
                        'کارت به کارت' => 'manual',
                    ],
                    'expanded' => true,
                    'multiple' => false,
                ]
            )
            ->add(
                'invoice_id',
                HiddenType::class
            );
    }
}
