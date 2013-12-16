<?php

namespace PlaygroundEmailCampaign\Service;

use ZfcBase\EventManager\EventProvider;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;

use PlaygroundEmailCampaign\Entity\Contact as ContactEntity;
use PlaygroundEmailCampaign\Mapper\Contact as ContactMapper;
use PlaygroundEmailCampaign\Mapper\Subscription as SubscriptionMapper;
use PlaygroundEmailCampaign\Entity\Subscription as SubscriptionEntity;
use Application\Form\Contact;

class Contact extends EventProvider implements ServiceManagerAwareInterface
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

    /**
     *
     * @param \PlaygroundUser\Entity\User $user
     * @return \PlaygroundEmailCampaign\Entity\Contact
     */
    public function create($user)
    {
        $contact = new ContactEntity();
        $contact->setUser($user);
        $contact->setOptin($user->getOptin());
        $contact = $this->getContactMapper()->insert($contact);

        //create on distant

        return $contact;
    }

    /**
     *
     * @param \PlaygroundEmailCampaign\Entity\Contact $contact
     * @return \PlaygroundEmailCampaign\Entity\Contact
     */
    public function edit($contact)
    {
        $contact = $this->getContactMapper()->update($contact);

        //edit on distant

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

    /**
     *
     * @param \PlaygroundEmailCampaign\Entity\Contact $contact
     * @param \PlaygroundEmailCampaign\Entity\MailingList $list
     * @return \PlaygroundEmailCampaign\Entity\Contact
     */
    public function createSubscription($contact, $list)
    {
        $subscription = new SubscriptionEntity();
        $subscription->setContact($contact);
        $subscription->setMailingList($mailingList);
        $subscription->setStatus(SubscriptionEntity::STATUS_PENDING);
        $subscription = $this->getSubscriptionMapper()->insert($subscription);

        return $subscription;
    }

    /**
     *
     * @param \PlaygroundEmailCampaign\Entity\Subscription $subscription
     * @return \PlaygroundEmailCampaign\Entity\Subscription
     */
    public function activateSubscription($subscription)
    {
        $subscription->setStatus(SubscriptionEntity::STATUS_SUBSCRIBED);
        $subscription = $this->getSubscriptionMapper()->update($subscription);

        return $subscription;
    }

    /**
     *
     * @param \PlaygroundEmailCampaign\Entity\Subscription $subscription
     * @return \PlaygroundEmailCampaign\Entity\Subscription
     */
    public function deactivateSubscription($subscription)
    {
        $subscription->setStatus(SubscriptionEntity::STATUS_UNSUBSCRIBED);
        $subscription = $this->getSubscriptionMapper()->update($subscription);

        return $subscription;
    }

    /**
     *
     * @param \PlaygroundEmailCampaign\Entity\Subscription $subscription
     * @return \PlaygroundEmailCampaign\Entity\Subscription
     */
    public function clearSubscription($subscription)
    {
        $subscription->setStatus(SubscriptionEntity::STATUS_CLEARED);
        $subscription = $this->getSubscriptionMapper()->update($subscription);

        return $subscription;
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