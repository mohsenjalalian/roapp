<?php

namespace AppBundle;

use AppBundle\DependencyInjection\Compiler\GatewayPass;
use AppBundle\DependencyInjection\Compiler\ShipmentPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class AppBundle
 * @package AppBundle
 */
class AppBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new GatewayPass());
        $container->addCompilerPass(new ShipmentPass());
    }
}
