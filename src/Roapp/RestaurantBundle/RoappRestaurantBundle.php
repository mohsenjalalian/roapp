<?php

namespace Roapp\RestaurantBundle;

use Roapp\RestaurantBundle\DependencyInjection\Compiler\ShipmentPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class RoappRestaurantBundle
 * @package Roapp\RestaurantBundle
 */
class RoappRestaurantBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new ShipmentPass());
    }
}
