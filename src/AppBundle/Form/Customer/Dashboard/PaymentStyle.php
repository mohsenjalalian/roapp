<?php

namespace AppBundle\Form\Customer\Dashboard;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\FormBuilderInterface;

class PaymentStyle extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('method',ChoiceType::class,[
                'choices' => [
                    'کارت به کارت' => 'manual',
                    'پرداخت در محل' => 'cash_on_delivery'
                ],
                'expanded' => true,
                'multiple' => false,
                'label' => 'روش های پرداخت :'
            ])
            ->add('invoice_id',HiddenType::class);
    }

}