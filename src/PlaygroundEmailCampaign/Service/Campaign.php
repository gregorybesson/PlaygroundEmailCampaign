<?php

namespace PlaygroundEmailCampaign\Service;

use ZfcBase\EventManager\EventProvider;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;

use PlaygroundEmailCampaign\Entity\Campaign as CampaignEntity;
use PlaygroundEmailCampaign\Mapper\Campaign as CampaignMapper;
use PlaygroundEmailCampaign\Service\WebMailFacade;

class Campaign extends EventProvider implements ServiceManagerAwareInterface
{
    /**
     * @var CampaignMapper
     */
    protected $campaignMapper;

    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    /**
     * @var WebMailFacade
     */
    protected $facadeService;

    public function create($campaign)
    {
        $campaignId = $this->getFacadeService()->addCampaign($campaign);
        if ($campaignId) {
            $campaign->setDistantId($campaignId);
            $campaign = $this->getCampaignMapper()->insert($campaign);
        }
        return $campaign;
    }

    public function edit($campaign)
    {
        if ($campaign->getDistantId()) {
            $campaignId = $this->getFacadeService()->updateCampaign($campaign);
        } else {
            $campaignId = $this->getFacadeService()->addCampaign($campaign);
        }
        if ($campaignId) {
            $campaign->setDistantId($campaignId);
            $campaign = $this->getCampaignMapper()->update($campaign);
        }
        return $campaign;
    }

    public function remove($campaignId)
    {
        $campaignMapper = $this->getCampaignMapper();
        $campaign = $campaignMapper->findById($campaignId);
        if (!$campaign) {
            return false;
        }
        // remove from WebMail
        $result = $this->getFacadeService()->deleteCampaign($campaign);
        if ($result) {
            // remove from local
            $campaignMapper->remove($campaign);
        }
        return $result;
    }

    // function schedule sending -> call create emails
    public function schedule($time)
    {

    }

    // fucntion send -> update email statuses
    public function send()
    {

    }

    // function create emails


    public function getServiceManager()
    {
        return $this->serviceManager;
    }

    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
        return $this;
    }

    public function getCampaignMapper()
    {
        if (null === $this->campaignMapper) {
            $this->campaignMapper = $this->getServiceManager()->get('playgroundemailcampaign_campaign_mapper');
        }
        return $this->campaignMapper;
    }

    public function setCampaignMapper($campaignMapper)
    {
        $this->campaignMapper = $campaignMapper;
        return $this;
    }

    public function getFacadeService()
    {
        if (null === $this->facadeService) {
            $this->facadeService = $this->getServiceManager()->get('playgroundemailcampaign_facade_service');
        }
        return $this->facadeService;
    }

    public function setFacadeService($facadeService)
    {
        $this->facadeService = $facadeService;
        return $this;
    }
}