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
     * @var ModuleOptions
     */
    protected $options;

    /**
     * @var TemplateMapper
     */
    protected $templateMapper;

    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    public function create($data = array())
    {
        $template = new TemplateEntity();
        $template->populate($data);
        $template = $this->getTemplateMapper()->insert($template);
        if (!$template) {
            return false;
        }
        return $this->update($template->getId(), $data);
    }

    public function edit($templateId, array $data)
    {
        // find by Id the corresponding template
        $template = $this->getTemplateMapper()->findById($templateId);
        if (!$template) {
            return false;
        }
        return $this->update($template->getId(), $data);
    }

    public function update($templateId, array $data)
    {
        $template = $this->getTemplateMapper()->findById($templateId);

        // Handle Image upload
        if (!empty($data['fileHtml']['tmp_name'])) {
            $path = $this->getOptions()->getMediaPath() . DIRECTORY_SEPARATOR;
            $real_media_path = realpath($path) . DIRECTORY_SEPARATOR;
            $media_url = $this->getOptions()->getMediaUrl() . '/';

            $oldTemplateURL = $template->getFileHtmlURL();
            ErrorHandler::start();
            $data['image']['name'] = 'template-' . $templateId . "-" . $data['fileHtml']['name'];
            move_uploaded_file($data['fileHtml']['tmp_name'], $path . $data['fileHtml']['name']);
            $template->setImageURl($media_url . $data['fileHtml']['name']);
            ErrorHandler::stop(true);

            if ($oldTemplateURL) {
                $real_media_path = realpath($path) . DIRECTORY_SEPARATOR;
                unlink(str_replace($media_url, $real_media_path, $oldTemplateURL));
            }
        }
        // handle witten code

        $template->populate($data);
        $this->getTemplateMapper()->update($template);

        // After saving in our own database, we need to save updates in the web mail's database

        return $template;
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
}