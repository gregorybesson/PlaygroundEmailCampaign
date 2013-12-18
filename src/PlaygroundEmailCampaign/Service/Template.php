<?php

namespace PlaygroundEmailCampaign\Service;

use ZfcBase\EventManager\EventProvider;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\Stdlib\ErrorHandler;

use PlaygroundEmailCampaign\Entity\Template as TemplateEntity;
use PlaygroundEmailCampaign\Mapper\Template as TemplateMapper;
use PlaygroundEmailCampaign\Options\ModuleOptions;

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

    public function update($templateId, $data)
    {
        $template = $this->getTemplateMapper()->findById($templateId);

        $path = $this->getOptions()->getMediaPath() . DIRECTORY_SEPARATOR;
        $real_media_path = realpath($path) . DIRECTORY_SEPARATOR;
        $media_url = $this->getOptions()->getMediaUrl() . '/';

        // Handle Image upload
        if (!empty($data['htmlFile']['tmp_name'])) {
            $oldTemplateURL = $template->getHtmlFileURL();
            ErrorHandler::start();
            $data['htmlFile']['name'] = 'template-' . $template->getId() . "-" . $data['htmlFile']['name'];
            $template->setHtmlFileURL($media_url . $data['htmlFile']['name']);
            ErrorHandler::stop(true);

            if ($oldTemplateURL) {
                $real_media_path = realpath($path) . DIRECTORY_SEPARATOR;
                unlink(str_replace($media_url, $real_media_path, $oldTemplateURL));
            }
        }
        // handle written code
        elseif ($data['htmlContent'] !== null) {
            if (!$template->getHtmlFileURL()) {
                // need to slugify the title !!
                $fileName = $template->getId() . "-" . $template->getTitle() . '.html';
                $template->setHtmlFileURL($media_url . $fileName);
            }
            // write content to file
            file_put_contents(str_replace($media_url, $real_media_path, $template->getHtmlFileURL()), $data['htmlContent']);
        }

        // Save updates in the web mail's database

        // Save in our own database
        $this->getTemplateMapper()->insert($template);

        return $template;
    }

    public function remove($templateId) {
        $templateMapper = $this->getTemplateMapper();
        $template = $templateMapper->findById($templateId);
        if (!$template) {
            return false;
        }
        if ($template->getHtmlFileURL()) {
            $path = $this->getOptions()->getMediaPath() . DIRECTORY_SEPARATOR;
            $real_media_path = realpath($path) . DIRECTORY_SEPARATOR;
            $media_url = $this->getOptions()->getMediaUrl() . '/';
            unlink(str_replace($media_url, $real_media_path, $template->getHtmlFileURL()));
        }
        // remove from WebMail

        // remove from local
        $templateMapper->remove($template);
        return true;
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