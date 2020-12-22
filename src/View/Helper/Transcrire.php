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


    /**
     * Display project infos area
     *
     * @return HTML
     */
    public function projectInfos()
    {
        $view = $this->getView();

        $itemSet = $view->project->itemSet();

        $publishers = $subjects = $types = $linkedResources = [];

        if ($publishersValues = $itemSet->value('dcterms:publisher', ['all' => true, 'type' => 'literal'])) {
            foreach($publishersValues as $publisher) {
                $publishers[] = $publisher;
            }
        }

        if ($subjectsValues = $itemSet->value('dcterms:subject', ['all' => true, 'type' => 'literal'])) {
            foreach($subjectsValues as $subject) {
                $subjects[] = $subject;
            }
        }

        if ($typesValues = $itemSet->value('dcterms:type', ['all' => true, 'type' => 'literal'])) {
            foreach($typesValues as $type) {
                $types[] = $type;
            }
        }

        if ($linkedResourcesValues = $itemSet->values()) {
            foreach($linkedResourcesValues as $value) {
                foreach($value['values'] as $v) {
                    if ($v->type() == 'uri') {
                        $linkedResources[$v->uri()] = (string)$v;
                    }
                }
            }
        }

        return $view->render(
            'project-infos',
            [
                'project'          => $view->project,
                'publishers'       => $publishers,
                'subjects'         => $subjects,
                'types'            => $types,
                'linkedResources'  => $linkedResources,
            ]);
    }

}
