<?php

namespace PlaygroundEmailCampaign\Service;

use ZfcBase\EventManager\EventProvider;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;

use PlaygroundEmailCampaign\Entity\MailingList as MailingListEntity;
use PlaygroundEmailCampaign\Mapper\MailingList as MailingListMapper;

class MailingList extends EventProvider implements ServiceManagerAwareInterface
{
    /**
     * @var MailingListMapper
     */
    protected $mailingListMapper;

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



    public function getMailingListMapper()
    {
        if (null === $this->mailingListMapper) {
            $this->mailingListMapper = $this->getServiceManager()->get('playgroundemailcampaign_mailinglist_mapper');
        }
        return $this->mailingListMapper;
    }

    public function setMailingListMapper($mailingListMapper)
    {
        $this->mailingListMapper = $mailingListMapper;
        return $this;
    }
}