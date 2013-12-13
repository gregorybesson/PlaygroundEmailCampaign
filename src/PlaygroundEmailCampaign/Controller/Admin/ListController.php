<?php

namespace PlaygroundEmailCampaign\Controller\Admin;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use PlaygroundEmailCampaign\Service\MailingList as MailingListService;

class ListController extends AbstractActionController
{
    /**
     *
     * @var MailingListService
     */
    protected $mailingListService;

    public function getMailingListService()
    {
        if ($this->mailingListService === null) {
            $this->mailingListService = $this->getServiceLocator()->get('playgroundemailcampaign_mailinglist_service');
        }
        return $this->mailingListService;
    }

    public function setMailingListService($mailingListService)
    {
        $this->mailingListService = $mailingListService;
        return $this;
    }
}