<?php

namespace AppBundle\Form\Customer\Dashboard;

 use Symfony\Component\Console\Input\Input;
 use Symfony\Component\Form\AbstractType;
 use Symfony\Component\Form\Extension\Core\Type\HiddenType;
 use Symfony\Component\Form\Extension\Core\Type\TextType;
 use Symfony\Component\Form\FormBuilderInterface;
 
 class PaymentManualType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
       $builder
           ->add('first_name',null,
               [
                   'label' => 'نام'
               ]
           )
           ->add('last_name',null,
               [
                   'label' => 'نام خانوداگی'
               ]
           )
           ->add('card_number',null,
               [
                   'label' => 'شماره کارت'
               ]
           )
           ->add('bank_name',null,
               [
                   'label' => 'نام بانک'
               ]
           )
           ->add('tracking_code',null,
               [
                  'label' => 'کد پیگیری'
               ]
           );
//           ->add('payment_id',HiddenType::class);
    }
}