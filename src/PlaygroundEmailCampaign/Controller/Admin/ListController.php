<?php

namespace PlaygroundEmailCampaign\Controller\Admin;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use Zend\Paginator\Paginator;
use PlaygroundCore\ORM\Pagination\LargeTablePaginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrineAdapter;

use PlaygroundEmailCampaign\Service\MailingList as MailingListService;
use PlaygroundEmailCampaign\Service\Contact as ContactService;

class ListController extends AbstractActionController
{
    /**
     *
     * @var MailingListService
     */
    protected $mailingListService;

    /**
     *
     * @var ContactService
     */
    protected $contactService;

    public function addAction()
    {
        $form = $this->getServiceLocator()->get('playgroundemailcampaign_mailinglist_form');
        $form->get('submit')->setLabel('Enregistrer');
        $form->setAttribute('action', '');

        $contacts = $this->getContactService()->getContactMapper()->findBy(array('optin'=>true));

        if ($this->getRequest()->isPost()) {
            $data = array_replace_recursive(
                $this->getRequest()->getPost()->toArray(),
                $this->getRequest()->getFiles()->toArray()
            );
            $form->setData($data);
            if ($form->isValid()) {
                $mailingList = $this->getMailingListService()->create($form->getData());
                if ($mailingList) {
                    return $this->redirect()->toRoute('admin/email-campaign/lists');
                }
            } else {
                foreach ($form->get('subscriptions')->getMessages() as $errMsg) {
                    foreach ($errMsg as $field => $msg) {
                        $this->flashMessenger()->addMessage($field . ' - ' . current($msg));
                    }
                }
                return $this->redirect()->toRoute('admin/email-campaign/lists/add');
            }
        }
        return new ViewModel(array(
            'form' => $form,
            'activeContacts' => $contacts,
            'flashMessages' => $this->flashMessenger()->getMessages(),
        ));
    }

    public function editAction()
    {
        $listId = $this->getEvent()->getRouteMatch()->getParam('listId');
        if (!$listId) {
            return $this->redirect()->toRoute('admin/email-campaign/lists');
        }
        $mailingList = $this->getMailingListService()->getMailingListMapper()->findById($listId);

        $form = $this->getServiceLocator()->get('playgroundemailcampaign_mailinglist_form');
        $form->get('submit')->setLabel("Enregistrer");
        $form->setAttribute('action', '');
        $form->bind($mailingList);

        $activeContacts = $this->getContactService()->getContactMapper()->findBy(array('optin'=>true));

        if ($this->getRequest()->isPost()) {
            $data = array_replace_recursive(
                $this->getRequest()->getPost()->toArray(),
                $this->getRequest()->getFiles()->toArray()
            );

            $form->setData($data);
            if ($form->isValid()) {
                $mailingList = $this->getMailingListService()->edit($mailingList->getId(), $form->getData()->getArrayCopy());
                if ($mailingList) {
                    return $this->redirect()->toRoute('admin/email-campaign/lists');
                }
            } else {
                foreach ($form->getMessages() as $field => $errMsg) {
                    $this->flashMessenger()->addMessage($field . ' - ' . current($errMsg));
                }
                return $this->redirect()->toRoute('admin/email-campaign/lists/add');
            }
        }

        $viewModel = new ViewModel();
        $viewModel->setTemplate('playground-email-campaign/list/add');
        $viewModel->setVariables(
            array(
                'form' => $form,
                'activeContacts' => $activeContacts,
                'flashMessages' => $this->flashMessenger()->getMessages(),
            )
        );
        return $viewModel;
    }

    public function removeAction()
    {
        $listId = $this->getEvent()->getRouteMatch()->getParam('listId');
        if (!$listId) {
            return $this->redirect()->toRoute('admin/email-campaign/lists');
        }
        $result = $this->getMailingListService()->remove($listId);
        if (!$result) {
            $this->flashMessenger()->addMessage('Une erreur est survenue pendant la suppression de la liste');
        } else {
            $this->flashMessenger()->addMessage('La liste a bien été supprimée');
        }
        return $this->redirect()->toRoute('admin/email-campaign/lists');
    }

    public function viewAction()
    {
        $list = $this->getEvent()->getRouteMatch()->getParam('listId');
        if (!$list) {
            return $this->redirect()->toRoute('admin/email-campaign/lists');
        }
        $subscriptions = $this->getMailingListService()->getSubscriptionMapper()->findBy(array('mailingList' => $list,));
        $adapter = new DoctrineAdapter(
            new LargeTablePaginator(
                $this->getMailingListService()->getMailingListMapper()->queryAll(array('name' => 'ASC'))
            )
        );
        $paginator = new Paginator($adapter);

        $paginator->setItemCountPerPage(25);
        $paginator->setCurrentPageNumber($this->getEvent()->getRouteMatch()->getParam('p'));

        return new ViewModel(array(
            'lists' => $paginator,
            'flashMessages' => $this->flashMessenger()->getMessages(),
        ));
    }

    public function listAction()
    {
        $adapter = new DoctrineAdapter(
            new LargeTablePaginator(
                $this->getMailingListService()->getMailingListMapper()->queryAll(array('name' => 'ASC'))
            )
        );
        $paginator = new Paginator($adapter);

        $paginator->setItemCountPerPage(25);
        $paginator->setCurrentPageNumber($this->getEvent()->getRouteMatch()->getParam('p'));

        return new ViewModel(array(
            'lists' => $paginator,
            'flashMessages' => $this->flashMessenger()->getMessages(),
        ));
    }

    //export action -> all emails form a list

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

    public function getContactService()
    {
        if ($this->contactService === null) {
            $this->contactService = $this->getServiceLocator()->get('playgroundemailcampaign_contact_service');
        }
        return $this->contactService;
    }

    public function setContactService($contactService)
    {
        $this->contactService = $contactService;
        return $this;
    }

}