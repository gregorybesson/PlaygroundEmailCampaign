<?php

namespace PlaygroundEmailCampaign\Service;

use ZfcBase\EventManager\EventProvider;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;

use PlaygroundEmailCampaign\Entity\Template as TemplateEntity;
use PlaygroundEmailCampaign\Mapper\Template as TemplateMapper;

class Template extends EventProvider implements ServiceManagerAwareInterface
{
    /**
     * @var TemplateMapper
     */
    protected $templateMapper;

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

    public function getTemplateMapper()
    {
        if (null === $this->templateMapper) {
            $this->templateMapper = $this->getServiceManager()->get('playgroundemailcampaign_template_mapper');
        }
        return $this->templateMapper;
    }

    public function setTemplateMapper($templateMapper)
    {
        $this->templateMapper = $templateMapper;
        return $this;
    }
}