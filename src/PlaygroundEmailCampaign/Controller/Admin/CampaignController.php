<?php

namespace PlaygroundEmailCampaign\Controller\Admin;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use Zend\Paginator\Paginator;
use PlaygroundCore\ORM\Pagination\LargeTablePaginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrineAdapter;

use PlaygroundEmailCampaign\Service\Campaign as CampaignService;
use PlaygroundEmailCampaign\Entity\Campaign as CampaignEntity;

class CampaignController extends AbstractActionController
{
    /**
     *
     * @var CampaignService
     */
    protected $campaignService;

    public function addAction()
    {
        $form = $this->getServiceLocator()->get('playgroundemailcampaign_campaign_form');
        $form->get('submit')->setLabel('Créer');
        $form->setAttribute('action', '');
        $campaign = new CampaignEntity();
        $form->bind($campaign);

        if ($this->getRequest()->isPost()) {
            $data = array_replace_recursive(
                $this->getRequest()->getPost()->toArray(),
                $this->getRequest()->getFiles()->toArray()
            );
            $form->setData($data);
            if ($form->isValid()) {
                $campaign = $this->getCampaignService()->create($form->getData());
                if ($campaign) {
                    return $this->redirect()->toRoute('admin/email-campaign/campaigns');
                }
            } else {
                foreach ($form->getMessages() as $field => $errMsg) {
                    $this->flashMessenger()->addMessage($field . ' - ' . current($errMsg));
                }
                return $this->redirect()->toRoute('admin/email-campaign/campaigns/add');
            }
        }
        return new ViewModel(array(
            'form' => $form,
            'flashMessages' => $this->flashMessenger()->getMessages(),
        ));
    }

    public function editAction()
    {
        $campaignId = $this->getEvent()->getRouteMatch()->getParam('campaignId');
        if (!$campaignId) {
            return $this->redirect()->toRoute('admin/email-campaign/campaigns');
        }
        $campaign = $this->getCampaignService()->getCampaignMapper()->findById($campaignId);
        if ($campaign->getIsSent()) {
            $this->flashMessenger()->addMessage('This campaign as been sent already, you can not modify it anymore');
                    return $this->redirect()->toRoute('admin/email-campaign/campaigns');
        }
        $form = $this->getServiceLocator()->get('playgroundemailcampaign_campaign_form');
        $form->get('submit')->setLabel("Enregistrer");
        $form->setAttribute('action', '');
        $form->bind($campaign);

        if ($this->getRequest()->isPost()) {
            $data = array_replace_recursive(
                $this->getRequest()->getPost()->toArray(),
                $this->getRequest()->getFiles()->toArray()
            );

            $form->setData($data);
            if ($form->isValid()) {
                $campaign = $this->getCampaignService()->edit($form->getData());
                if ($campaign) {
                    return $this->redirect()->toRoute('admin/email-campaign/campaigns');
                }
            } else {
                foreach ($form->getMessages() as $field => $errMsg) {
                    $this->flashMessenger()->addMessage($field . ' - ' . current($errMsg));
                }
                return $this->redirect()->toRoute('admin/email-campaign/campaigns/edit', array('campaignId' => $campaign->getId())));
            }
        }

        $viewModel = new ViewModel();
        $viewModel->setTemplate('playground-email-campaign/campaign/add');
        $viewModel->setVariables(
            array(
                'form' => $form,
                'flashMessages' => $this->flashMessenger()->getMessages(),
            )
        );
        return $viewModel;
    }

    public function removeAction()
    {
        $campaignId = $this->getEvent()->getRouteMatch()->getParam('campaignId');
        if (!$campaignId) {
            return $this->redirect()->toRoute('admin/email-campaign/campaigns');
        }
        $result = $this->getCampaignService()->remove($campaignId);
        if (!$result) {
            $this->flashMessenger()->addMessage('Une erreur est survenue pendant la suppression de la campagne');
        } else {
            $this->flashMessenger()->addMessage('La campagne a bien été supprimée');
        }
        return $this->redirect()->toRoute('admin/email-campaign/campaigns');
    }

    public function listAction()
    {
        $adapter = new DoctrineAdapter(
            new LargeTablePaginator(
                $this->getCampaignService()->getCampaignMapper()->queryAll(array('createdAt' => 'DESC'))
            )
        );
        $paginator = new Paginator($adapter);

        $paginator->setItemCountPerPage(25);
        $paginator->setCurrentPageNumber($this->getEvent()->getRouteMatch()->getParam('p'));

        return new ViewModel(array(
            'campaigns' => $paginator,
            'flashMessages' => $this->flashMessenger()->getMessages(),
        ));
    }

    public function scheduleAction()
    {

        return new ViewModel(array(
            'flashMessages' => $this->flashMessenger()->getMessages(),
        ));
    }

    public function getCampaignService()
    {
        if ($this->campaignService === null) {
            $this->campaignService = $this->getServiceLocator()->get('playgroundemailcampaign_campaign_service');
        }
        return $this->campaignService;
    }

    public function setCampaignService($campaignService)
    {
        $this->campaignService = $campaignService;
        return $this;
    }
}