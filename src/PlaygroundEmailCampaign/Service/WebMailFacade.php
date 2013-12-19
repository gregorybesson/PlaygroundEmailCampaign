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

    }

    public function subscribeList($list, $contacts)
    {

    }

    public function unsubscribeList($list, $contact)
    {
    }

    public function deleteList($list)
    {
    }

    public function listLists()
    {
        return $this->getWebMailService()->listLists();
    }

    //setUp si en local / changement de service
    // new service -> pour tous !!
    //tout ceux qui n'ont pas de id distant : création sur le web mail


    //dans l'autre sens : import depuis le web mail des entités

    // init contact -> all users to contact

    // createContact(pgUser)

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