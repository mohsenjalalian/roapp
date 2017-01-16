<?php

namespace AppBundle\Form\Security;

use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;

/**
 * Class RecoveryPasswordType
 * @package AppBundle\Form\Security
 */
class RecoveryPasswordType extends AbstractType
{
    private $translator;

    /**
     * RecoveryPasswordType constructor.
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'newPassword',
                RepeatedType::class,
                [
                    'type' => PasswordType::class,
                    'invalid_message' => $this->translator->trans('different_password_error'),
                    'options' => array('attr' => array('class' => 'password-field')),
                    'required' => true,
                    'mapped' => false,
                    'first_options'  => array('label' => 'رمز عبور'),
                    'second_options' => array('label' => 'تکرار رمز عبور'),
                    'constraints' => [
                        new Length([
                            'min' => 6,
                            'max' => 12,
                            'minMessage' => $this->translator->trans('min_password_char_error'),
                            'maxMessage' => $this->translator->trans('max_password_char_error'),
                        ]),
                    ],
                ]
            );
    }
}
