<?php

namespace PlaygroundEmailCampaign\Controller\Admin;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use Zend\Paginator\Paginator;
use PlaygroundCore\ORM\Pagination\LargeTablePaginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrineAdapter;

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
        if (!$this->getTemplateService()->getFacadeService()->checkConnection() ) {
            $this->flashMessenger("Sorry, the distant service can not be accessed, try it latter");
            return $this->redirect()->toRoute('admin/email-campaign/templates');
        }
        $form = $this->getServiceLocator()->get('playgroundemailcampaign_template_form');
        $form->get('submit')->setLabel('Ajouter');

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

    public function editAction()
    {
        if (!$this->getTemplateService()->getFacadeService()->checkConnection() ) {
            $this->flashMessenger("Sorry, the distant service can not be accessed, try it latter");
            return $this->redirect()->toRoute('admin/email-campaign/templates');
        }

        $templateId = $this->getEvent()->getRouteMatch()->getParam('templateId');
        if (!$templateId) {
            return $this->redirect()->toRoute('admin/email-campaign/templates');
        }
        $template = $this->getTemplateService()->getTemplateMapper()->findById($templateId);

        $form = $this->getServiceLocator()->get('playgroundemailcampaign_template_form');
        $form->get('submit')->setLabel("Modifier");
        $form->setAttribute('action', '');
        $form->bind($template);

//         $htmlContent = '';
        $preview = $template->getPreviewURL();

//         if ($template->getHtmlFileURL()) {
//             $media_url = $this->getTemplateService()->getOptions()->getMediaUrl() . DIRECTORY_SEPARATOR;
//             $path = $this->getTemplateService()->getOptions()->getMediaPath() . DIRECTORY_SEPARATOR;
//             $real_media_path = realpath($path) . DIRECTORY_SEPARATOR;
//             $doc = new \DOMDocument();
//             $doc->loadHTMLFile(str_replace($media_url, $real_media_path, $template->getHtmlFileURL()));
//             if ($doc) {
//                 $preview_content = '';
//                 foreach ($doc->getElementsByTagName('body')->item(0)->childNodes as $child) {
//                     $preview_content .= $child->C14N();
//                 }
//                 $preview_attributes = '';
//                 foreach ($doc->getElementsByTagName('body')->item(0)->attributes as $attr) {
//                     $preview_attributes .= $attr->name . '="' . $attr->value . '" ';
//                 }
//                 $form->get('htmlContent')->setValue(utf8_decode($doc->C14N()));
//             }
//         }

        if ($this->getRequest()->isPost()) {
            $data = array_replace_recursive(
                $this->getRequest()->getPost()->toArray(),
                $this->getRequest()->getFiles()->toArray()
            );

            $form->setData($data);
            if ($form->isValid()) {
                $data = array_replace_recursive($data, $form->getData()->getArrayCopy());
                $template = $this->getTemplateService()->edit($template->getId(), $data);
                if ($template) {
                    return $this->redirect()->toRoute('admin/email-campaign/templates');
                }
            } else {
                foreach ($form->getMessages() as $field => $errMsg) {
                    $this->flashMessenger()->addMessage($field . ' - ' . current($errMsg));
                }
                return $this->redirect()->toRoute('admin/email-campaign/templates/edit', array('templateId' => $template->getId()));
            }
        }

        $viewModel = new ViewModel();
        $viewModel->setTemplate('playground-email-campaign/template/add');
        $viewModel->setVariables(
            array(
                'form' => $form,
                'flashMessages' => $this->flashMessenger()->getMessages(),
//                 'preview_attributes' => $preview_attributes,
//                 'preview_content' => $preview_content,
                'preview' =>$preview,
            )
        );
        return $viewModel;
    }

    public function removeAction()
    {
        if (!$this->getTemplateService()->getFacadeService()->checkConnection() ) {
            $this->flashMessenger("Sorry, the distant service can not be accessed, try it latter");
            return $this->redirect()->toRoute('admin/email-campaign/templates');
        }
        $templateId = $this->getEvent()->getRouteMatch()->getParam('templateId');
        if (!$templateId) {
            return $this->redirect()->toRoute('admin/email-campaign/templates');
        }
        $result = $this->getTemplateService()->remove($templateId);
        if (!$result) {
            $this->flashMessenger()->addMessage('Une erreur est survenue pendant la suppression du template');
        } else {
            $this->flashMessenger()->addMessage('Le template a bien été supprimé');
        }
        return $this->redirect()->toRoute('admin/email-campaign/templates');
    }

    public function listAction()
    {
        if (!$this->getTemplateService()->getFacadeService()->checkConnection() ) {
            $this->flashMessenger("Sorry, the distant service can not be accessed, try it latter");
            return $this->redirect()->toRoute('admin/email-campaign/templates');
        }
        $adapter = new DoctrineAdapter(
            new LargeTablePaginator(
                $this->getTemplateService()->getTemplateMapper()->queryAll(array('title' => 'ASC'))
            )
        );
        $paginator = new Paginator($adapter);

        $paginator->setItemCountPerPage(25);
        $paginator->setCurrentPageNumber($this->getEvent()->getRouteMatch()->getParam('p'));

        return new ViewModel(array(
            'templates' => $paginator,
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