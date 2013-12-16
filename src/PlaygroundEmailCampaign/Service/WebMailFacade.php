<?php

namespace PlaygroundEmailCampaign\Service;

use ZfcBase\EventManager\EventProvider;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;

use PlaygroundEmailCampaign\Options\ModuleOptions;

use PlaygroundEmailCampaign\Service\MailChimpService;
use PlaygroundEmailCampaign\Service\Template as TemplateService;

class WebMailFacade extends EventProvider implements ServiceManagerAwareInterface
{
    /**
     * @var ModuleOptions
     */
    protected $options;

    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    protected $webMailService;

    /**
     *
     * @var TemplateService
     */
    protected $templateService;

    public function getQueryURL()
    {
        return $this->getWebMailService()->getQueryURL();
    }


    //setUp si en local / changement de service
    // new service -> pour tous !!
    //tout ceux qui n'ont pas de id distant : création sur le web mail


    //dans l'autre sens : import depuis le web mail des entités

    // init contact -> all users to contact

    // createContact(pgUser)


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

    public function getServiceManager()
    {
        return $this->serviceManager;
    }

    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
        return $this;
    }

    public function getOptions()
    {
        if (!$this->options instanceof ModuleOptions) {
            $this->setOptions($this->getServiceManager()->get('playgroundemailcampaign_module_options'));
        }
        return $this->options;
    }

    public function setOptions(ModuleOptions $options)
    {
        $this->options = $options;
        return $this;
    }

    public function getWebMailService()
    {
        if ($this->getOptions()->getService() == "MailChimp") {
            $this->setWebMailService($this->getServiceManager()->get('playgroundemailcampaign_mailchimp_service'));
        }
        return $this->webMailService;
    }

    public function setWebMailService($webMailService)
    {
        $this->webMailService = $webMailService;
        return $this;
    }
}