<?php

namespace PlaygroundEmailCampaign\Service;

use ZfcBase\EventManager\EventProvider;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\Stdlib\ErrorHandler;

use PlaygroundEmailCampaign\Entity\Template as TemplateEntity;
use PlaygroundEmailCampaign\Mapper\Template as TemplateMapper;
use PlaygroundEmailCampaign\Options\ModuleOptions;
use PlaygroundEmailCampaign\Service\WebMailFacade;

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

    /**
     * @var WebMailFacade
     */
    protected $facadeService;

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

        if (!empty($data['htmlFile']['tmp_name'])) {
            $fileContent = $this->getFacadeService()->import($data['htmlFile']);
            if ($fileContent) {
                $template->setHtmlContent($fileContent);
            }
        }

//         if (!empty($data['htmlFile']['tmp_name'])) {
//             $path = $this->getOptions()->getMediaPath() . DIRECTORY_SEPARATOR;
//             $real_media_path = realpath($path) . DIRECTORY_SEPARATOR;
//             $media_url = $this->getOptions()->getMediaUrl() . '/';
//             $oldTemplateURL = $template->getHtmlFileURL();
//             ErrorHandler::start();
//             $data['htmlFile']['name'] = 'template-' . $template->getId() . "-" . $data['htmlFile']['name'];
//             $template->setHtmlFileURL($media_url . $data['htmlFile']['name']);
//             ErrorHandler::stop(true);

//             $content = file_get_contents();

//             if ($oldTemplateURL) {
//                 $real_media_path = realpath($path) . DIRECTORY_SEPARATOR;
//                 unlink(str_replace($media_url, $real_media_path, $oldTemplateURL));
//             }
//         }

//         // handle written code
//         elseif ($data['htmlContent'] !== null) {
//             if (!$template->getHtmlFileURL()) {
//                 // need to slugify the title !!
//                 $fileName = $template->getId() . "-" . $template->getTitle() . '.html';
//                 $template->setHtmlFileURL($media_url . $fileName);
//             }
//             // write content to file
//             $doc = new \DOMDocument();
//             $doc->preserveWhiteSpace = false;
//             file_put_contents(str_replace($media_url, $real_media_path, $template->getHtmlFileURL()), utf8_encode($data['htmlContent']));
//         }

        // Save updates in the web mail's database
        if (!$template->getDistantId()) {
            $result = $this->getFacadeService()->addTemplate($template);
            if ($result) {
                $template->setDistantId($result);
            }
        } else {
            $this->getFacadeService()->updateTemplate($template);
        }
        $template->setPreviewURL($this->getFacadeService()->previewTemplate($template));
        // Save in our own database
        $this->getTemplateMapper()->insert($template);

        return $template;
    }

    public function getStruct($template)
    {
        return $this->getFacadeService()->getStructTemplate($template);
    }

    public function listAll()
    {
//         return $this->getFacadeService()->listCustomTemplates();
        return $this->getFacadeService()->listDefaultTemplates();
    }

    public function remove($templateId)
    {
        $templateMapper = $this->getTemplateMapper();
        $template = $templateMapper->findById($templateId);
        if (!$template) {
            return false;
        }
//         if ($template->getHtmlFileURL()) {
//             $path = $this->getOptions()->getMediaPath() . DIRECTORY_SEPARATOR;
//             $real_media_path = realpath($path) . DIRECTORY_SEPARATOR;
//             $media_url = $this->getOptions()->getMediaUrl() . '/';
//             unlink(str_replace($media_url, $real_media_path, $template->getHtmlFileURL()));
//         }
        // remove from WebMail
        $result = $this->getFacadeService()->deleteTemplate($template);
        if ($result) {
            // remove from local
            $templateMapper->remove($template);
            return true;
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

    public function getFacadeService()
    {
        if (null === $this->facadeService) {
            $this->facadeService = $this->getServiceManager()->get('playgroundemailcampaign_facade_service');
        }
        return $this->facadeService;
    }

    public function setFacadeService($facadeService)
    {
        $this->facadeService = $facadeService;
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