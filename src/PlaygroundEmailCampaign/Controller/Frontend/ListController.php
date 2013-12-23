<?php

namespace PlaygroundEmailCampaign\Controller\Frontend;

use PlaygroundEmailCampaign\Service\WebMailfacade;
use Zend\View\Model\ViewModel;
use Zend\Mvc\Controller\AbstractActionController;

class ListController extends AbstractActionController
{
    /**
     * @var WebMailfacade
     */
    protected $facadeService;

    public function optinAction()
    {
        $sm = $this->getServiceLocator();
        $contactService = $sm->get('playgroundemailcampaign_contact_service');
        $contactService->initContactBook();
        $contacts = $contactService->getContactMapper()->findAll();
        $listService = $sm->get('playgroundemailcampaign_mailinglist_service');
        $lists = $listService->getMailingListMapper()->findAll();
        $list = current($lists);
        foreach($contacts as $contact) {
//             $contact->setOptin(0);
//             $contactService->getContactMapper()->update($contact);
            $subscription = $listService->createSubscription($contact, $list);
        }
        return new ViewModel(array());
    }

    public function optoutAction()
    {
        return new ViewModel(array());
    }

    public function getFacadeService()
    {
        if ($this->facadeService === null) {
            $this->facadeService = $this->getServiceLocator()->get('playgroundemailcampaign_facade_service');
        }
        return $this->facadeService;
    }

    public function setFacadeService(WebMailfacade $facadeService)
    {
        $this->facadeService = $facadeService;
        return $this;
    }

}