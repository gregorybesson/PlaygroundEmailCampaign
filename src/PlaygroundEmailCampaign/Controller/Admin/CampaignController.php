<?php

namespace PlaygroundEmailCampaign\Controller\Admin;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use PlaygroundEmailCampaign\Service\Campaign as CampaignService;

class CampaignController extends AbstractActionController
{
    /**
     *
     * @var CampaignService
     */
    protected $campaignService;

    public function getCampaignService()
    {
        if ($this->campaignService === null) {
            $this->campaignService = $this->getServiceLocator()->get('playgroundemailcampaign_campaign_service');
        }
        return $this->campaignService;
    }

    public function setCampaignService($campaignService)
    {
        $this->campaignService = $campaignService;
        return $this;
    }
}