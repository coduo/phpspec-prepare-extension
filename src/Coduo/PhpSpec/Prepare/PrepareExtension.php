<?php

namespace Coduo\PhpSpec\Prepare;

use Coduo\PhpSpec\Prepare\Runner\Maintainer\BeforeMaintainer;
use PhpSpec\Extension\ExtensionInterface;
use PhpSpec\ServiceContainer;

class PrepareExtension implements ExtensionInterface
{
    /**
     * @param ServiceContainer $container
     */
    public function load(ServiceContainer $container)
    {
        $container->set('runner.maintainers.prepare_before', function ($c) {
            return new BeforeMaintainer($c->get('unwrapper'));
        });
    }
}
