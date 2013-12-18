<?php

namespace PlaygroundEmailCampaign\Service;

use ZfcBase\EventManager\EventProvider;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;

use PlaygroundEmailCampaign\Options\ModuleOptions;
use Assetic\Exception\Exception;

class MailChimpService extends EventProvider implements ServiceManagerAwareInterface
{
    /**
     * @var ModuleOptions
     */
    protected $options;

    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    /**
     * @var \Mailchimp
     */
    protected $mc;

    public function __construct()
    {
        $key = $this->getOptions()->getUserKey();
        try {
            $this->mc = new \Mailchimp($key);
        } catch (Mailchimp_Error $e) {
            throw new \Exception('No API key provided');
        }
    }

    /***TEMPLATES***/
    public function addTemplate($title, $htmlStructure)
    {
        $this->mc->templates->add($title, $htmlStructure);
    }

    public function updateTemplate($title, $htmlStructure)
    {
        $this->mc->templates->update($title, $htmlStructure);
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

    public function getServiceManager()
    {
        return $this->serviceManager;
    }

    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
        return $this;
    }

    public function getMc()
    {
        return $this->mc;
    }

    public function setMc(\Mailchimp $mc)
    {
        $this->mc = $mc;
        return $this;
    }
}