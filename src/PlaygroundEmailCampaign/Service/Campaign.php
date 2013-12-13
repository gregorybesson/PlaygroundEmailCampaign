<?php

namespace PlaygroundEmailCampaign\Service;

use ZfcBase\EventManager\EventProvider;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;

use PlaygroundEmailCampaign\Entity\Campaign as CampaignEntity;
use PlaygroundEmailCampaign\Mapper\Campaign as CampaignMapper;

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
}