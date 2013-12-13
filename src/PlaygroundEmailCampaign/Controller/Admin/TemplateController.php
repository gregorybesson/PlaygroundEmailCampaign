<?php

namespace PlaygroundEmailCampaign\Controller\Admin;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

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
        $template = new TemplateEntity();
        $form->bind($template);

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