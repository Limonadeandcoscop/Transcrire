<?php
namespace Transcrire\View\Helper;

use Laminas\Router\Http\RouteMatch;
use Laminas\View\Helper\AbstractHelper;
use Scripto\Mediawiki\ApiClient;
use Scripto\ControllerPlugin\Scripto;

/**
 * View helper used to render Transcrire template elements.
 */
class Transcrire extends AbstractHelper
{

    /**
     * @var Scripto controller plugin
     */
    protected $scriptoPlugin;

    /**
     * @var Scripto API Client
     */
    protected $apiClient;

    /**
     * @var RouteMatch
     */
    protected $routeMatch;


    /**
     * @param RouteMatch $routeMatch
     */
    public function __construct(Scripto $scriptoPlugin, ApiClient $apiClient, RouteMatch $routeMatch)
    {
        $this->scriptoPlugin    = $scriptoPlugin;
        $this->routeMatch       = $routeMatch;
        $this->apiClient        = $apiClient;
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


    /**
     * Display latest contributions
     *
     * @return HTML
     */
    public function latestContributions() {

        $view = $this->getView();
        $currentProjectId = $view->project->id();

        $hours = 1440;
        $start = null;

        $response = $this->apiClient->queryRecentChanges($hours, 100, $start);

        $recentTranscriptions = $this->scriptoPlugin->prepareMediawikiList($response['query']['recentchanges']);

        $latestContributions = [];

        foreach ($recentTranscriptions as $key => $recentTranscription) {

            if (empty($recentTranscription['scripto_media'])) continue;

            $projectId = $recentTranscription['scripto_media']->scriptoItem()->scriptoProject()->id();

            if ($projectId != $currentProjectId) continue;

            $latestContributions[] = $recentTranscription;
        }

        return $view->render('latest-contributions', ['latestContributions' => $latestContributions]);
    }
}
