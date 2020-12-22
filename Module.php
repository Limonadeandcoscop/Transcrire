<?php
namespace Transcrire;

use Omeka\Module\AbstractModule;
use Laminas\Mvc\MvcEvent;
use Laminas\EventManager\Event;
use Laminas\EventManager\SharedEventManagerInterface;
use Omeka\Site\Theme\Theme;

class Module extends AbstractModule
{
    /**
     * Include module configuration
     */
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
}
