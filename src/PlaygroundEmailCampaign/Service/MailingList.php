<?php

namespace PlaygroundEmailCampaign\Service;

use ZfcBase\EventManager\EventProvider;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use PlaygroundEmailCampaign\Service\WebMailFacade;

use PlaygroundEmailCampaign\Entity\MailingList as MailingListEntity;
use PlaygroundEmailCampaign\Mapper\MailingList as MailingListMapper;
use PlaygroundEmailCampaign\Mapper\Conatct as ContactMapper;
use PlaygroundEmailCampaign\Entity\Subscription as SubscriptionEntity;
use PlaygroundEmailCampaign\Mapper\Subscription as SubscriptionMapper;

class MailingList extends EventProvider implements ServiceManagerAwareInterface
{
    /**
     * @var MailingListMapper
     */
    protected $mailingListMapper;

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
        $listId = $this->getFacadeService()->addList($mailingList);
        if (!$listId) {
            return false;
        }
        $mailingList->setDistantId($listId);
        $mailingList = $this->getMailingListMapper()->insert($mailingList);
        return $mailingList;
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

        $removed = $this->getFacadeService()->deleteList($mailingList);
        if ($removed) {
            $mailingListMapper->remove($mailingList);
        }
        return $removed;
    }

    public function listAll()
    {
        return $this->getFacadeService()->listLists();
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
        $subscription = $this->getSubscriptionMapper()->isRegistered($list, $contact);
        if ($contact->getOptin() && !$subscription) {
            $subscription = new SubscriptionEntity();
            $subscription->setMailingList($list);
            //subscribe on distant service
            $subscribe = $this->getFacadeService()->subscribeList($list, $contact);

            if ($subscribe) {
                $contact->setDistantID($subscribe);
                $contact = $this->getContactMapper()->update($contact);
                $subscription->setContact($contact);
                $subscription->setStatus(SubscriptionEntity::STATUS_SUBSCRIBED);
                $subscription = $this->getSubscriptionMapper()->insert($subscription);
                return $subscription;
            }
        } elseif ($contact->getOptin() && $subscription) {
            return $this->activateSubscription($subscription);
        }
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

        //subscribe on distant service
        $subscribe = $this->getFacadeService()->subscribeList($subscription->getMailingList(), $subscription->getContact());

        return $subscription;
    }

    /**
     *
     * @param \PlaygroundEmailCampaign\Entity\Subscription $subscription
     * @return \PlaygroundEmailCampaign\Entity\Subscription
     */
    public function deactivateSubscription($contact, $list)
    {
        $subscription = $this->getSubscriptionMapper()->findOneBy(array(
            'mailingList' => $list,
            'contact' => $contact,
        ));
        $this->getFacadeService()->unsubscribeList($list, $contact, false);
        $subscription->setStatus(SubscriptionEntity::STATUS_UNSUBSCRIBED);
        $subscription = $this->getSubscriptionMapper()->update($subscription);


        return $subscription;
    }

    /**
     *
     * @param \PlaygroundEmailCampaign\Entity\Subscription $subscription
     * @return \PlaygroundEmailCampaign\Entity\Subscription
     */
    public function clearSubscription($contact, $list)
    {
        $subscription = $this->getSubscriptionMapper()->findOneBy(array(
            'mailingList' => $list,
            'contact' => $contact,
        ));
        // CLEAR FOR DISTANT
        $this->getFacadeService()->unsubscribeList($list, $contact, true);
        if ($subscription) {
            $subscription->setStatus(SubscriptionEntity::STATUS_CLEARED);
            $subscription = $this->getSubscriptionMapper()->update($subscription);
        }
        return $subscription;
    }

    public function removeSubscription($subscription)
    {
        $this->getFacadeService()->unsubscribeList($subscription->getMailingList(), $subscription->getContact(), true);
        $this->getSubscriptionMapper()->remove($subscription);
        return true;
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