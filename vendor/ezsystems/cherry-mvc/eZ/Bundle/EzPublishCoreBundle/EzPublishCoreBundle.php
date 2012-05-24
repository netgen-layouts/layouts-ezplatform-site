<?php
/**
 * File containing the EzPublishCoreBundle class.
 *
 * @copyright Copyright (C) 1999-2012 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace eZ\Bundle\EzPublishCoreBundle;

use eZ\CherryMvc\Routing\Matcher\UrlMatcher;
use eZ\CherryMvc\Template\TwigEngine;
use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Compiler\AddFieldTypePass;
use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Compiler\RegisterStorageEnginePass;
use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Compiler\LegacyStorageEnginePass;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class EzPublishCoreBundle extends Bundle
{
    public function boot()
    {
        // Adding the router listener
        // TODO: This should not be defined here as routing should be made configurable like in Symfony Standard Edition (routing.yml)
        $routes = include $this->container->getParameter( 'kernel.root_dir' ) . '/config/routes.php';

        $dispatcher = $this->container->get( 'event_dispatcher' );
        $dispatcher->addSubscriber(
            new RouterListener(
                new UrlMatcher(
                    $routes,
                    new RequestContext(),
                    $dispatcher
                )
            )
        );

        // Registering the Template engine.
        // TODO: This should be done with DIC with the help of a tag
        // TODO: We might need to consider Templating component instead of this
        $tplFactory = $this->container->get( 'ezpublish.templating.factory' );
        $tplFactory->register(
            "twig",
            new TwigEngine()
        );
    }

    public function build( ContainerBuilder $container )
    {
        parent::build( $container );
        $container->addCompilerPass( new AddFieldTypePass );
        $container->addCompilerPass( new RegisterStorageEnginePass );
        $container->addCompilerPass( new LegacyStorageEnginePass );
    }
}
