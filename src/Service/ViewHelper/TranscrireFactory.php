<?php
namespace Transcrire\Service\ViewHelper;

use Transcrire\View\Helper\Transcrire;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class TranscrireFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $services, $requestedName, array $options = null)
    {
        return new Transcrire(
            $services->get('Application')->getMvcEvent()->getRouteMatch()
        );
    }
}
