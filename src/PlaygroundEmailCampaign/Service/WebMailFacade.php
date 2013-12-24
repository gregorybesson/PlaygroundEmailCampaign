<?php

namespace PlaygroundEmailCampaign\Service;

use ZfcBase\EventManager\EventProvider;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\Stdlib\ErrorHandler;

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

    /**** GENERAL ****/

    public function init()
    {
        // stuffs
    }

    /**
     * @return boolean weither we can connect or not to the web mail service
     */
    public function checkConnection()
    {
        return $this->getWebMailService()->ping();
    }

    /**** TEMPLATES ****/

    /**
     *
     * @param Template $template
     * @return  integer template_id if it worked
     *          boolean false if it failed
     */
    public function addTemplate($template)
    {
        return $this->getWebMailService()->addTemplate($template);
    }

    /**
     *
     * @param Template $template
     * @return boolean weither or not the update worked
     */
    public function updateTemplate($template)
    {
        return $this->getWebMailService()->updateTemplate($template);
    }

    public function previewTemplate($template)
    {
        $result = $this->getWebMailService()->getPreviewTemplate($template);
    }

    public function getStructTemplate($template)
    {
        return $this->getWebMailService()->getSectionsTemplate($template);
    }

    /**
     *
     * @param Template $template
     * @return boolean weither or not the template was deleted
     */
    public function deleteTemplate($template)
    {
        return $this->getWebMailService()->deleteTemplate($template);
    }

    /**
     *
     * @return array of arrays
     */
    public function listDefaultTemplates()
    {
        return $this->getWebMailService()->listDefaultTemplates();
    }

    /**
     *
     * @return array of arrays
     */
    public function listCustomTemplates()
    {
        return $this->getWebMailService()->listCustomTemplates();
    }

    /**** LISTS ****/

    /**
     *
     * @param MailingList $list
     * @return integer list_id if it worked
     *          boolean false if it failed
     */
    public function addList($list)
    {
        return $this->getWebMailService()->createList($list);
    }

    /**
     *
     * @param MailingList $list
     * @return boolean weither or not the function was called properly
     */
    public function deleteList($list)
    {
        return $this->getWebMailService()->deleteList($list);
    }

    /**
     *
     * @param MailingList $list
     * @param Contact $contact
     * @param boolen $sendEmail weither or not we send an email to the user so he needs to confirm the subscription
     * @return  string contact distant id if it was successful
     *          boolean false if it was not
     */
    public function subscribeList($list, $contact, $sendEmail = false)
    {
        return $this->getWebMailService()->subscribeList($list, $contact, false);
    }

    /**
     *
     * @param MailingList $list
     * @param Contact $contact
     * @return boolean weither it worked or not
     */
    public function unsubscribeList($list, $contact, $clearSubscription = false)
    {
        return $this->getWebMailService()->unsubscribeList($list, $contact, $clearSubscription);
    }

    /**
     *
     * @return array of arrays
     */
    public function listLists()
    {
        return $this->getWebMailService()->listLists();
    }

    /**** CAMPAIGNS ****/

    /**
     *
     * @param Campaign $campaign
     * @return integer list_id if it worked
     *          boolean false if it failed
     */
    public function addCampaign($campaign)
    {
        return $this->getWebMailService()->addCampaign($campaign);
    }

    /**
     *
     * @param Campaign $campaign
     * @return boolean weither it worked or not
     */
    public function updateCampaign($campaign)
    {
        return $this->getWebMailService()->updateCampaign($campaign);
    }

    /**
     *
     * @param Campaign $campaign
     * @return boolean weither it was called properly or not
     */
    public function deleteCampaign($campaign)
    {
        return $this->getWebMailService()->deleteCampaign($campaign);
    }

    /**
     *
     * @param Campaign $campaign
     * @return boolean weither the campaign is ready to be sent or not
     */
    public function readyToSendCampaign($campaign)
    {
        return $this->getWebMailService()->readyToSendCampaign($campaign);
    }

    /**
     *
     * @param Campaign $campaign campaign which we want to replicate
     * @return  integer id of the replicated campaign (if it worked)
     *          boolean false (if it failed)
     */
    public function replicateCampaign($campaign)
    {
        return $this->getWebMailService()->replicateCampaign($campaign);
    }

    public function sendCampaign($campaign)
    {
        return $this->getWebMailService()->sendCampaign($campaign);
    }

    /**** OTHER ****/
    public function import($fileData) {
        if (!empty($fileData['tmp_name'])) {
            $path = $this->getOptions()->getMediaPath() . DIRECTORY_SEPARATOR;
            $real_media_path = realpath($path) . DIRECTORY_SEPARATOR;

            // use the xml data as object
            ErrorHandler::start();
            move_uploaded_file($fileData['tmp_name'], $path . $fileData['name']);
            ErrorHandler::stop(true);

            if (!file_exists($real_media_path.$fileData['name'])) {
                return false;
            }

            $content = file_get_contents($real_media_path.$fileData['name']);

            if ($content) {
                // remove the file from folder
                unlink($real_media_path.$fileData['name']);
                return $content;
            }
        }
        return false;
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