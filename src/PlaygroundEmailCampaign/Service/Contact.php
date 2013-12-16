<?php

namespace PlaygroundEmailCampaign\Service;

use ZfcBase\EventManager\EventProvider;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;

use PlaygroundEmailCampaign\Entity\Contact as ContactEntity;
use PlaygroundEmailCampaign\Mapper\Contact as ContactMapper;
use Application\Form\Contact;

class Contact extends EventProvider implements ServiceManagerAwareInterface
{
    /**
     * @var ContactMapper
     */
    protected $contactMapper;

    // if user -> optout, call unsuscbribe = all its subscriptions to unsubscribe state + unsuscribe in web mail list

    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    public function create($user)
    {
        $contact = new ContactEntity();
        $contact->setUser($user);
        $contact->setOptin($user->getOptin());
    }

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