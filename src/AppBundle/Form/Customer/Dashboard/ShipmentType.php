<?php

namespace AppBundle\Form\Customer\Dashboard;

use AppBundle\Form\DataTransformer\DateTimeTransformer;
use Doctrine\ORM\EntityRepository;
use jDateTime;
use Roapp\MediaBundle\Form\RoappImageType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Console\Input\Input;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ShipmentType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('description', null,
                array(
                    "label"=>"توضیحات",
                    'translation_domain' => 'messages',
                ))
            ->add('value', null,
                array(
                    "label"=>"ارزش بسته",
                    'translation_domain' => 'messages'
                ))

            ->add('pickUpTime', TextType::class,
                array(
                    "label"=>"تعیین زمان تحویل",
                    'translation_domain' => 'messages',
                    'attr' => ['class' => 'js-datepicker']
                ))

            ->add('other',TextType::class,
                array(
                    "label"=>"شماره گیرنده",
                    'attr' => ['placeHolder'=> 'لطفا شماره تلفن همراه مورد نظر خود را وارد نمایید.'],
                    'translation_domain' => 'messages',
                    'mapped' => false
                ))
            ->add('photoFiles', RoappImageType::class)
        ;
        $builder->get('pickUpTime')
            ->addModelTransformer(new DateTimeTransformer());
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Shipment'
        ));
    }
}
