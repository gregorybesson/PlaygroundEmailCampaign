<?php

namespace PlaygroundEmailCampaign\Service;

use ZfcBase\EventManager\EventProvider;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;

use PlaygroundEmailCampaign\Entity\Contact as ContactEntity;
use PlaygroundEmailCampaign\Mapper\Contact as ContactMapper;
use PlaygroundUser\Service\User as UserService;
use PlaygroundEmailCampaign\Mapper\Subscription as SubscriptionMapper;
use PlaygroundEmailCampaign\Entity\Subscription as SubscriptionEntity;

class Contact extends UserService implements ServiceManagerAwareInterface
{
    /**
     * @var ContactMapper
     */
    protected $contactMapper;

    /**
     * @var SubscriptionMapper
     */
    protected $subscriptionMapper;

    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    public function __construct()
    {
    }

    public function initContactBook()
    {
        $users = $this->getUserMapper()->findAll();
        foreach ($users as $user) {
            $this->createContact($user);
        }
    }

    /**
     *
     * @param \PlaygroundUser\Entity\User $user
     * @return \PlaygroundEmailCampaign\Entity\Contact
     */
    public function createContact($user)
    {
        $contact = $this->getContactMapper()->isRegistered($user);
        if (!$contact) {
            $contact = new ContactEntity();
            $contact->setUser($user);
            $contact->setOptin($user->getOptin());
        }
        $contact = $this->getContactMapper()->insert($contact);

        return $contact;
    }

    /**
     *
     * @param \PlaygroundEmailCampaign\Entity\Contact $contact
     * @return \PlaygroundEmailCampaign\Entity\Contact
     */
    public function editContact($contact)
    {
        $contact = $this->getContactMapper()->update($contact);

        return $contact;
    }

    /**
     *
     * @param \PlaygroundEmailCampaign\Entity\Contact $contact
     * @return \PlaygroundEmailCampaign\Entity\Contact
     */
    public function setOptout($contact)
    {
        $contact->setOptin('0');
        $subscriptions = $this->getSubscriptionMapper()->queryByContact($contact);
        foreach ($subscriptions as $subscription) {
            $this->deactivateSubscription($subscription);
        }
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

    public function getUserService()
    {
        return $this->userService;
    }

    public function setUserService($userService)
    {
        $this->userService = $userService;
        return $this;
    }

    public function getSubscriptionMapper()
    {
        if (null === $this->subscriptionMapper) {
            $this->subscriptionMapper = $this->getServiceManager()->get('playgroundemailcampaign_subscription_mapper');
        }
        return $this->subscriptionMapper;
    }

    public function setSubscriptionMapper($subscriptionMapper)
    {
        $this->subscriptionMapper = $subscriptionMapper;
        return $this;
    }
}