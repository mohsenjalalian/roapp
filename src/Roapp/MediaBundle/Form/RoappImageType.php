<?php

namespace Roapp\MediaBundle\Form;

use AppBundle\Entity\Media;
use Doctrine\ORM\EntityManager;
use Roapp\MediaBundle\Form\UploadTransformer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Roapp\MediaBundle\Annotation\UploadableField;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;

class RoappImageType extends AbstractType
{
    private $roappMediaUploads;

    /**
     * @var Reader
     */
    private $reader;

    /**
     * @var Container
     */
    private $container;

    /**
     * @var string
     */
    private $mediaName;

    public function __construct($roappMediaUploads, Container $container)
    {
        $this->roappMediaUploads = $roappMediaUploads;
        $this->container = $container;
        $this->reader = $container->get('annotation_reader');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'media_name' => null
        ]);
    }

    public function getParent()
    {
        return HiddenType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $mediaName = $this->mediaName;
        $mediaData = $this->roappMediaUploads[$mediaName];
        $url = $this->container->get('router')->generate(
            '_upload',
            [
                'prefix' => $mediaData['path'],
                'mediaName' => $mediaName
            ]
        );

        $linkTemplateUrl = $this->container->get('router')->generate(
            '_link',
            [
                'prefix' => $mediaData['path'],
                'mediaName' => $mediaName,
                'media' => '__replaceme__'
            ]
        );
        
        $view->vars['link_template_url'] = $linkTemplateUrl;
        $view->vars['path'] = $url;
        $view->vars['directory'] = $form->getConfig()->getOption('media_name');
        $view->vars['parallel_uploads'] = $mediaData['parallel_uploads'];
        $view->vars['max_filesize'] = $mediaData['max_filesize'];
        $view->vars['max_files'] = $mediaData['max_files'];
        $view->vars['accepted_files'] = $mediaData['accepted_files'];
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options); // TODO: Change the autogenerated stub
        
        $self = $this;

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use($self, $options) {
            $self->initMediaName($event->getForm(), $options);
        });

        $builder->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) use($self, $options) {
            $self->initMediaName($event->getForm(), $options);
        });

        $builder->addModelTransformer(new UploadTransformer($this->container, $self));
    }

    private function initMediaName(FormInterface $form, $options)
    {
        if ($options['mapped'] && !empty($options['media_name'])) {
            throw new \Exception(sprintf(
                'media_name option must not be set for mapped form field.',
                $options['media_name']
            ));
        }

        if (!$options['mapped'] && empty($options['media_name'])) {
            throw new \Exception(sprintf(
                'media_name option must not be empty for non mapped form field.',
                $options['media_name']
            ));
        }

        if ($options['mapped'] && empty($options['media_name'])) {
            $reflectionClass = new \ReflectionClass($form->getRoot()->getData());
            $reflectionProperty = $reflectionClass->getProperty($form->getName());
            /** @var UploadableField $propertyAnnotation */
            $propertyAnnotation = $this->reader->getPropertyAnnotation($reflectionProperty, UploadableField::class);

            if (!$propertyAnnotation instanceof UploadableField) {
                throw new \Exception(sprintf(
                    'The attribute has not UploadableField annotaion.'
                ));
            }

            if (empty($propertyAnnotation->getMappedAttribute())) {
                throw new \Exception('mapped attribute must not be null');
            }

            if (empty($propertyAnnotation->getMediaName())) {
                throw new \Exception('media name must not be null');
            }

            $mediaName = $propertyAnnotation->getMediaName();
        } elseif (!$options['mapped'] && !empty($options['media_name'])) {
            if (!isset($this->roappMediaUploads[$options['media_name']])) {
                throw new \Exception(sprintf(
                    '%s is not valid upload media.',
                    $options['media_name']
                ));
            }

            $mediaName = $options['media_name'];
        }

        $this->mediaName = $mediaName;
    }

    /**
     * @return string
     */
    public function getMediaName() {
        return $this->mediaName;
    }
}
