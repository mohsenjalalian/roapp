<?php
/**
 * User: mohsenjalalian
 * Date: 12/18/16
 * Time: 6:18 PM
 */
namespace AppBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class EntityToIdTransformer
 * @package AppBundle\Form\DataTransformer
 */
class EntityToIdTransformer implements DataTransformerInterface
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;
    /**
     * @var string
     */
    protected $class;

    /**
     * EntityToIdTransformer constructor.
     * @param ObjectManager $objectManager
     * @param mixed         $class
     */
    public function __construct(ObjectManager $objectManager, $class)
    {
        $this->objectManager = $objectManager;
        $this->class = $class;
    }

    /**
     * @param mixed $entity
     * @return integer|null
     */
    public function transform($entity)
    {
        if (null === $entity) {
            return null;
        }

        return $entity->getId();
    }

    /**
     * @param mixed $id
     * @return null|object
     */
    public function reverseTransform($id)
    {
        if (!$id) {
            return null;
        }
        $entity = $this->objectManager
            ->getRepository($this->class)
            ->find($id);
        if (null === $entity) {
            throw new TransformationFailedException();
        }

        return $entity;
    }
}
