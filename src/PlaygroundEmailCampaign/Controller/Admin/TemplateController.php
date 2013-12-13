<?php

namespace PlaygroundEmailCampaign\Controller\Admin;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use PlaygroundEmailCampaign\Service\Template as TemplateService;

class TemplateController extends AbstractActionController
{
    /**
     *
     * @var TemplateService
     */
    protected $templateService;

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