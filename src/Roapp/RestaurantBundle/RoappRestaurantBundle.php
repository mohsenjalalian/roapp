<?php

namespace Roapp\RestaurantBundle;

use AppBundle\Utils\BusinessTypeBundleInterface;
use Roapp\RestaurantBundle\Entity\Restaurant;
use Roapp\RestaurantBundle\Entity\RestaurantShipment;
use Roapp\RestaurantBundle\Form\RestaurantShipmentType;
use Roapp\RestaurantBundle\Form\RestaurantType;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class RoappRestaurantBundle
 * @package Roapp\RestaurantBundle
 */
class RoappRestaurantBundle extends Bundle implements BusinessTypeBundleInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
    }

    /**
     * @return mixed
     */
    public static function getShipmentFormNamespace()
    {
        return RestaurantShipmentType::class;
    }

    /**
     * @return mixed
     */
    public static function getShipmentEntityNamespace()
    {
        return RestaurantShipment::class;
    }

    /**
     * @return string
     */
    public static function getBusinessTypeName()
    {
        return 'Restaurant';
    }

    /**
     * @return string
     */
    public static function getBusinessUnitFormNamespace()
    {
        return RestaurantType::class;
    }

    /**
     * @return string
     */
    public static function getBusinessUnitEntityNamespace()
    {
        return Restaurant::class;
    }

    /**
     * @return string
     */
    public static function getBusinessTypeSingleShipmentTitle()
    {
        return 'سفارش';
    }

    /**
     * @return string
     */
    public static function getBusinessTypePluralShipmentTitle()
    {
        return 'سفارش ها';
    }

    /**
     * @return string
     */
    public static function getBusinessTypePersianName()
    {
        return 'رستوران';
    }
}
