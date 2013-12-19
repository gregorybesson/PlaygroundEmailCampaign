<?php

namespace PlaygroundEmailCampaign\Service;

use ZfcBase\EventManager\EventProvider;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use PlaygroundEmailCampaign\Service\WebMailFacade;

use PlaygroundEmailCampaign\Entity\MailingList as MailingListEntity;
use PlaygroundEmailCampaign\Mapper\MailingList as MailingListMapper;
use PlaygroundEmailCampaign\Mapper\Subscription as SubscriptionMapper;

class MailingList extends EventProvider implements ServiceManagerAwareInterface
{
    /**
     * @var MailingListMapper
     */
    protected $mailingListMapper;

    /**
     * @var SubscriptionMapper
     */
    protected $subscriptionMapper;

    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    /**
     * @var WebMailFacade
     */
    protected $facadeService;

    //import from mailchimp ->check emails to know if pg user and create contact only if it is ??

    public function getServiceManager()
    {
        return $this->serviceManager;
    }

    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
        return $this;
    }

    public function create(array $data)
    {
        $mailingList = new MailingListEntity();
        $mailingList->populate($data);
        var_dump('creating');
        $mailingList = $this->getMailingListMapper()->insert($mailingList);

        var_dump($mailingList);
        if (!$mailingList) {
            return false;
        }
        return $this->update($mailingList->getId(), $data);
    }

    public function edit($mailingListId, array $data)
    {
        return $this->update($mailingListId, $data);
    }

    public function update($mailingListId, array $data)
    {
        $mailingList = $this->getMailingListMapper()->findById($mailingListId);
        if (!$mailingList) {
            return false;
        }
        $mailingList->populate($data);

        //handle subscriptions !

        //create/updata distant

        $this->getMailingListMapper()->update($mailingList);

        return $mailingList;
    }

    public function remove($mailingListId) {
        $mailingListMapper = $this->getMailingListMapper();
        $mailingList = $mailingListMapper->findById($mailingListId);
        if (!$mailingList) {
            return false;
        }

        //remove from distant

        $mailingListMapper->remove($mailingList);
        return true;
    }

    // do not allow to create a subscription if user is in optout
    /**
     *
     * @param \PlaygroundEmailCampaign\Entity\Contact $contact
     * @param \PlaygroundEmailCampaign\Entity\MailingList $list
     * @return \PlaygroundEmailCampaign\Entity\Contact
     */
    public function createSubscription($contact, $list)
    {
        if ($contact->getOptin()) {
            $subscription = new SubscriptionEntity();
            $subscription->setContact($contact);
            $subscription->setMailingList($mailingList);
            $subscription->setStatus(SubscriptionEntity::STATUS_PENDING);

            //subscribe on distant service


            $subscription = $this->getSubscriptionMapper()->insert($subscription);

            return $subscription;
        }
        return false;
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
    public function clearSubscription($list, $contact)
    {
        $subscription = $this->getSubscriptionMapper()->findOneBy(array(
            'mailingList' => $list,
            'contact' => $contact,
        ));
        // CLEAR FOR DISTANT

        if ($subscription) {
            $subscription->setStatus(SubscriptionEntity::STATUS_CLEARED);
            $subscription = $this->getSubscriptionMapper()->update($subscription);
        }
        return $subscription;
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