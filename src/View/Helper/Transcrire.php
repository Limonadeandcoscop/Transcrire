<?php
namespace Transcrire\View\Helper;

use Laminas\Router\Http\RouteMatch;
use Laminas\View\Helper\AbstractHelper;

/**
 * View helper used to render Transcrire template elements.
 */
class Transcrire extends AbstractHelper
{

    /**
     * Display project infos area
     *
     * @return HTML
     */
    public function projectInfos()
    {
        $view = $this->getView();
        $projectId = $view->project->id();
        return $view->render('project-infos');
    }

    /**
     * @var RouteMatch
     */
    protected $routeMatch;


    /**
     * @param RouteMatch $routeMatch
     */
    public function __construct(RouteMatch $routeMatch)
    {
        $this->routeMatch = $routeMatch;
    }
}
