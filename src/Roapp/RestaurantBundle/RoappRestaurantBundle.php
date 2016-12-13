<?php

namespace Roapp\RestaurantBundle;

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
    }
}
