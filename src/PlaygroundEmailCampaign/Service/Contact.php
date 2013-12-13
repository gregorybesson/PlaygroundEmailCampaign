<?php

namespace PlaygroundEmailCampaign\Service;

use ZfcBase\EventManager\EventProvider;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;

use PlaygroundEmailCampaign\Entity\Contact as ContactEntity;
use PlaygroundEmailCampaign\Mapper\Contact as ContactMapper;

class Contact extends EventProvider implements ServiceManagerAwareInterface
{
    /**
     * @var ContactMapper
     */
    protected $contactMapper;

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

    public function getContactMapper()
    {
        if (null === $this->contactMapper) {
            $this->contactMapper = $this->getServiceManager()->get('playgroundemailcampaign_contact_mapper');
        }
        return $this->contactMapper;
    }

    public function setContactMapper($contactMapper)
    {
        $this->contactMapper = $contactMapper;
        return $this;
    }
}