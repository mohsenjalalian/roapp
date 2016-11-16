<?php
/**
 * Created by PhpStorm.
 * User: msm
 * Date: 11/15/16
 * Time: 2:05 PM
 */

namespace AppBundle\Form\Customer\Dashboard;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ValidationCodeType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder 
            ->add('exchange_code',null,[
                'label' => 'کد را وارد نمایید',
            ]);
    }
}