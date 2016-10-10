<?php

namespace AppBundle\Form\Customer\Dashboard;

use AppBundle\Form\DataTransformer\DateTimeTransformer;
use jDateTime;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
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
                    "label"=>"form.description",
                    'translation_domain' => 'messages',
                ))
            ->add('value', null,
                array(
                    "label"=>"form.value",
                    'translation_domain' => 'messages'
                ))
//            ->add('status')
            ->add('pickUpTime', TextType::class,
                array(
                    "label"=>"form.pickUpTime",
                    'translation_domain' => 'messages',
                    'attr' => ['class' => 'js-datepicker']
                ))
//            ->add('createdAt', DateTimeType::class)
//            ->add('price')
//            ->add('type')
            ->add('ownerAddress', null,
                array(
                    "label"=>"form.ownerAddress",
                    'translation_domain' => 'messages'
                ))
            ->add('otherAddress', null,
                array(
                    "label"=>"other.address",
                    'translation_domain' => 'messages',
                    'mapped' => false
                ))
        ;
        $builder->get('pickUpTime')
            ->addModelTransformer(new DateTimeTransformer());
//            ->addModelTransformer(new CallbackTransformer(
//            function ($gDate) {
//                if (empty($gDate)) {
//                    return $gDate;
//                }
//                return $gDate->format("Y-m-d H:i:s");
//            },
//            function ($jdate) {
//                $date = explode("-",substr($jdate,0,10));
//                $time = substr($jdate,11,-3);
//                $gregorianArr = jDateTime::toGregorian($date[0],$date[1],$date[2]);
//                $gregorianDate = new \DateTime(
//                    sprintf(
//                        "%s-%s-%s %s",
//                        $gregorianArr[0],
//                        $gregorianArr[1],
//                        $gregorianArr[2],
//                        $time
//                    )
//                );
//                return $gregorianDate;
////                // transform the string back to an array
////                return explode(',', $tagsAsString);
//            }
//        ));
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
