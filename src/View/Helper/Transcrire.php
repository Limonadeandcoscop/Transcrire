<?php
namespace Transcrire\View\Helper;

use Laminas\Router\Http\RouteMatch;
use Laminas\View\Helper\AbstractHelper;
use Scripto\Mediawiki\ApiClient;
use Scripto\ControllerPlugin\Scripto;
use Scripto\Api\Representation\ScriptoItemRepresentation;

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
     * Display item infos area
     *
     * @return HTML
     */
    public function itemInfos()
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
            'item-infos',
            [
                'item'             => $view->item,
                'project'          => $view->project,
                'publishers'       => $publishers,
                'subjects'         => $subjects,
                'types'            => $types,
                'linkedResources'  => $linkedResources,
                'itemProgressBar'  => $this->itemProgressBar(),
            ]);
    }


    /**
     * Display media & item infos area
     *
     * @return HTML
     */
    public function mediaInfos()
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
            'media-infos',
            [
                'item'             => $view->item,
                'sMedia'           => $view->sMedia,
                'project'          => $view->project,
                'publishers'       => $publishers,
                'subjects'         => $subjects,
                'types'            => $types,
                'linkedResources'  => $linkedResources,
                'itemProgressBar'  => $this->itemProgressBar(),
            ]);
    }


    /**
     * Display item's progress bar
     *
     * @return HTML
     */
    private function itemProgressBar()
    {
        $view = $this->getView();
        $item = $view->sItem;

        $response = $view->api()->search('scripto_media', ['scripto_item_id' => $item->id()]);
        $sMedias = $response->getContent();
        $nbMedias = $item->mediaCount();

        $mediasStatus = [];
        if (count($sMedias)) {
            foreach($sMedias as $sMedia) {
                @$mediasStatus[$sMedia->status()]++;
            }
        }

        $nbInProgress = $percentInProgress = $nbCompleted = $percentCompleted = $nbNew = $percentNew = $nbApproved = $percentApproved = 0;

        $nbInProgress = @$mediasStatus[ScriptoItemRepresentation::STATUS_IN_PROGRESS];
        $percentInProgress = $nbMedias == 0 ?  0 : $nbInProgress / $nbMedias * 100;

        $nbApproved = @$mediasStatus[ScriptoItemRepresentation::STATUS_APPROVED];
        $percentApproved = $nbMedias == 0 ?  0 : $nbApproved / $nbMedias * 100;

        $nbCompleted = @$mediasStatus[ScriptoItemRepresentation::STATUS_COMPLETED];
        $percentCompleted = $nbMedias == 0 ?  0 : $nbCompleted / $nbMedias * 100;

        $nbNew = @$mediasStatus[ScriptoItemRepresentation::STATUS_NEW];
        $percentNew = $nbMedias == 0 ?  0 : $nbNew / $nbMedias * 100;

        return $view->render(
            'item-progress-bar',
            [
                'scripto_item'          => $item,
                'nb_medias'             => $nbMedias,
                'nb_in_progress'        => $nbInProgress,
                'percent_in_progress'   => $percentInProgress,
                'nb_approved'           => $nbApproved,
                'percent_approved'      => $percentApproved,
                'nb_completed'          => $nbCompleted,
                'percent_completed'     => $percentCompleted,
                'nb_new'                => $nbNew,
                'percent_new'           => $percentNew
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


     /**
     * Display item metadata
     *
     * @return HTML
     */
    public function itemMetadata()
    {
        $view = $this->getView();
        $item = $view->item;
        $values = $item->values();
        $metadata = [];

        foreach ($values as $key => $v) {
            $metadata[$key]['label'] = $v['property']->label();
            $i = 0;
            foreach ($v['values'] as $value) {
                $metadata[$key]['values'][$i]['type'] = $value->type();
                $metadata[$key]['values'][$i]['value'] = (string)$value;
                if ($value->type() == 'uri') {
                    $metadata[$key]['values'][$i]['uri'] = $value->uri();
                }
                $i++;
            }
        }

        return $view->render('item-metadata', ['metadata' => $metadata]);
    }


    /**
     * Display item contributions
     *
     * @return HTML
     */
    public function itemContributions()
    {
        $view = $this->getView();
        $sItem = $view->sItem;
        $currentItemId = $sItem->id();

        $hours = 1440;
        $start = null;

        $response = $this->apiClient->queryRecentChanges($hours, 100, $start);

        $recentTranscriptions = $this->scriptoPlugin->prepareMediawikiList($response['query']['recentchanges']);

        $itemContributions = [];

        foreach ($recentTranscriptions as $key => $recentTranscription) {

            if (empty($recentTranscription['scripto_media'])) continue;

            $itemId = $recentTranscription['scripto_media']->scriptoItem()->id();

            if ($itemId != $currentItemId) continue;

            $itemContributions[] = $recentTranscription;
        }

        return $view->render('item-contributions', ['itemContributions' => $itemContributions]);
    }


    /**
     * Display workspace area
     *
     * @return HTML
     */
    public function workspace()
    {
        $user = $this->apiClient->queryUserInfo();

        $view = $this->getView();
        $sMedia = $view->sMedia;
        $sItem = $view->sItem;
        $sProject = $sItem->scriptoProject();

        return $view->render('workspace', ['user' => $user, 'sMedia' => $sMedia, 'sProject' => $sProject]);
    }

    public function recentChanges()
    {
        $hours = 1440;
        $start = null;

        $response = $this->apiClient->queryRecentChanges($hours, 100, $start);

        $recentTranscriptions = $this->scriptoPlugin->prepareMediawikiList($response['query']['recentchanges']);

        foreach ($recentTranscriptions as $key => $recentTranscription) {
            if (@!$recentTranscription['scripto_media']) {
                unset($recentTranscriptions[$key]);
            }
        }

        $recentTranscriptionsByUser = [];
        $users = [];
        foreach ($recentTranscriptions as $key => $recentTranscription) {
            $user = $recentTranscription['user'];
            @$users[$user]++;
            $scripto_media_id = $recentTranscription['scripto_media']->id();
            // unset($recentTranscription['scripto_media']); // For testing
            $recentTranscriptionsByUser[$user][$scripto_media_id][]  = $recentTranscription;
        }

        $max = count($recentTranscriptions);
        if ($max > 20) $max = 20;

        $nbUsers = count($users); // Nombre de users

        $nbTranscriptions = 0;

        if (count($recentTranscriptions) <= 20) { // Less than 20 transcriptions, display all results

            $results = $recentTranscriptions;

        } elseif ($nbUsers >= 20) { // More than 20 users, display one transcription for each user

            // TODO

        } else {

         // echo '<pre>';
         // print_r($recentTranscriptionsByUser);

            $loop = 0;
            do {
                $loop++;
                if ($loop == $max) break;

                $userId = array_key_first($recentTranscriptionsByUser);
                $mediasOfUser = array_shift($recentTranscriptionsByUser);
                // print_r($mediasOfUser);

                $mediaId = array_key_first($mediasOfUser);
                $transcriptionsOfMedia = array_shift($mediasOfUser);

                if (is_array($transcriptionsOfMedia[0])) {
                    $results[] = $transcriptionsOfMedia[0];
                    unset($recentTranscriptionsByUser[$userId][$mediaId]);
                    $nbTranscriptions++;
                } else {
                    unset($recentTranscriptionsByUser[$userId]);
                }

            } while ($nbTranscriptions <= $max);
        }

        $recentTranscriptions = $results;

        $view = $this->getView();
        return $view->render('recent-changes', ['results' => $results]);
    }
}
