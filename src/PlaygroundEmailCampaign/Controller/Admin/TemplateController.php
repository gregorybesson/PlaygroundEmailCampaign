<?php

namespace PlaygroundEmailCampaign\Controller\Admin;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use Zend\Paginator\Paginator;
use PlaygroundCore\ORM\Pagination\LargeTablePaginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrineAdapter;

use PlaygroundEmailCampaign\Service\Template as TemplateService;
use PlaygroundEmailCampaign\Entity\Template as TemplateEntity;

class TemplateController extends AbstractActionController
{
    /**
     *
     * @var TemplateService
     */
    protected $templateService;

    public function addAction()
    {
        $form = $this->getServiceLocator()->get('playgroundemailcampaign_template_form');
        $form->get('submit')->setLabel('Ajouter');

        if ($this->getRequest()->isPost()) {
            $data = array_replace_recursive(
                $this->getRequest()->getPost()->toArray(),
                $this->getRequest()->getFiles()->toArray()
            );
            $form->setData($data);
            if ($form->isValid()) {
                $template = $this->getTemplateService()->create($form->getData());
                if ($template) {
                    return $this->redirect()->toRoute('admin/email-campaign/templates');
                }
            } else {
                foreach ($form->getMessages() as $field => $errMsg) {
                    $this->flashMessenger()->addMessage($field . ' - ' . current($errMsg));
                }
                return $this->redirect()->toRoute('admin/email-campaign/templates/add');
            }
        }
        return new ViewModel(array(
            'form' => $form,
            'flashMessages' => $this->flashMessenger()->getMessages(),
        ));
    }

    public function editAction()
    {
        $templateId = $this->getEvent()->getRouteMatch()->getParam('templateId');
        if (!$templateId) {
            return $this->redirect()->toRoute('admin/email-campaign/templates');
        }
        $template = $this->getTemplateService()->getTemplateMapper()->findById($templateId);
        $form = $this->getServiceLocator()->get('playgroundemailcampaign_template_form');
        $form->get('submit')->setLabel("Modifier");
        $form->setAttribute('action', '');
        $form->bind($template);

        if ($this->getRequest()->isPost()) {
            $data = array_replace_recursive(
                $this->getRequest()->getPost()->toArray(),
                $this->getRequest()->getFiles()->toArray()
            );

            $form->setData($data);
            if ($form->isValid()) {
                $template = $this->getTemplateService()->edit($template->getId(), $form->getData()->getArrayCopy());
                if ($template) {
                    return $this->redirect()->toRoute('admin/email-campaign/templates');
                }
            } else {
                foreach ($form->getMessages() as $field => $errMsg) {
                    $this->flashMessenger()->addMessage($field . ' - ' . current($errMsg));
                }
                return $this->redirect()->toRoute('admin/email-campaign/templates/add');
            }
        }

        $viewModel = new ViewModel();
        $viewModel->setTemplate('playground-email-campaign/template/add');
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
        $templateId = $this->getEvent()->getRouteMatch()->getParam('templateId');
        if (!$templateId) {
            return $this->redirect()->toRoute('admin/email-campaign/templates');
        }
        $result = $this->getTemplateService()->remove($templateId);
        if (!$result) {
            $this->flashMessenger()->addMessage('Une erreur est survenue pendant la suppression du template');
        } else {
            $this->flashMessenger()->addMessage('Le template a bien été supprimé');
        }
        return $this->redirect()->toRoute('admin/email-campaign/templates');
    }

    public function listAction()
    {
        $adapter = new DoctrineAdapter(
            new LargeTablePaginator(
                $this->getTemplateService()->getTemplateMapper()->queryAll(array('title' => 'ASC'))
            )
        );
        $paginator = new Paginator($adapter);

        $paginator->setItemCountPerPage(25);
        $paginator->setCurrentPageNumber($this->getEvent()->getRouteMatch()->getParam('p'));

        return new ViewModel(array(
            'templates' => $paginator,
            'flashMessages' => $this->flashMessenger()->getMessages(),
        ));
    }

    public function getTemplateService()
    {
        if ($this->templateService === null) {
            $this->templateService = $this->getServiceLocator()->get('playgroundemailcampaign_template_service');
        }
        return $this->templateService;
    }

    public function setTemplateService($templateService)
    {
        $this->templateService = $templateService;
        return $this;
    }
}