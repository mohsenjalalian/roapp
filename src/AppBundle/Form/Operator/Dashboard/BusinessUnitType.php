<?php

namespace AppBundle\Form\Operator\Dashboard;

use AppBundle\DBAL\EnumContractType;
use AppBundle\Entity\Operator;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class CustomerType
 * @package AppBundle\Form\Operator\Dashboard
 */
class BusinessUnitType extends AbstractType
{
    /** @var  TokenStorageInterface */
    private $tokenStorage;

    /**
     * BusinessUnitType constructor.
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $this->tokenStorage->getToken()->getUser();
        $builder
            ->add(
                'name',
                null,
                [
                    'label' => 'نام',
                ]
            )
            ->add(
                'businessType',
                null,
                [
                    'required' => true,
                    'label' => 'صنف',
                ]
            )
            ->addEventListener(
                FormEvents::PRE_SET_DATA,
                function (FormEvent $event) use ($user) {
                    $form = $event->getForm();
                    if ($user instanceof Operator) {
                        $form->add(
                            'contractType',
                            ChoiceType::class,
                            [
                                'label' => 'نوع قرار داد',
                                'choices' => array_combine(array_values(EnumContractType::getValues()), array_keys(EnumContractType::getValues())),
                            ]
                        );
                    }
                }
            )
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
