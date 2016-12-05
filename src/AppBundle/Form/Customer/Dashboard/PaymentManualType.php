<?php

namespace AppBundle\Form\Customer\Dashboard;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class PaymentManualType
 * @package AppBundle\Form\Customer\Dashboard
 */
class PaymentManualType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'first_name',
                null,
                [
                    'label' => 'نام',
                ]
            )
            ->add(
                'last_name',
                null,
                [
                    'label' => 'نام خانوداگی',
                ]
            )
            ->add(
                'card_number',
                null,
                [
                    'label' => 'شماره کارت',
                ]
            )
            ->add(
                'bank_name',
                null,
                [
                    'label' => 'نام بانک',
                ]
            )
            ->add(
                'tracking_code',
                null,
                [
                    'label' => 'کد پیگیری',
                ]
            );
    }
}
