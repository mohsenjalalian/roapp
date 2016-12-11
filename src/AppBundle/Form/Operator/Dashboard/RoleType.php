<?php

namespace AppBundle\Form\Operator\Dashboard;

use AppBundle\Entity\Person;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\Bundle\DoctrineBundle\Registry as DoctrineRegistry;

/**
 * Class RoleType
 * @package AppBundle\Form\Operator\Dashboard
 */
class RoleType extends AbstractType
{
    /**
     * @var
     */
    private $doctrine;

    /**
     * RoleType constructor.
     * @param \Doctrine\Bundle\DoctrineBundle\Registry $doctrine
     */
    public function __construct(DoctrineRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
        $this->scopes = $this->doctrine->getManager()->getClassMetadata(Person::class)->subClasses;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, [
                'label' => 'نقش',
            ])
            ->add('label', null, [
                'label' => 'عنوان',
            ])
            ->add('scope', ChoiceType::class, [
                'label' => 'حوزه',
                'choices' => $this->scopes,
                'choice_label' => function ($value, $key, $index) {
                    return explode('\\', $value)[2];
                },

            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Role',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_role';
    }
}
