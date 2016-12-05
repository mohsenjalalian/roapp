<?php

namespace AppBundle\Form\Customer\Dashboard;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class ValidationCodeType
 * @package AppBundle\Form\Customer\Dashboard
 */
class ValidationCodeType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'exchange_code',
                null,
                [
                    'label' => 'کد را وارد نمایید',
                ]
            );
    }
}
