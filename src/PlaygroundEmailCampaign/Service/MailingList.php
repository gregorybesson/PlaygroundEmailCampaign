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
}