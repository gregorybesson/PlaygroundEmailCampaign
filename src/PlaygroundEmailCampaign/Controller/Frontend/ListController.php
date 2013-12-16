<?php

namespace PlaygroundEmailCampaign\Controller\Frontend;

use PlaygroundEmailCampaign\Service\WebMailfacade;
use Zend\View\Model\ViewModel;
use Zend\Mvc\Controller\AbstractActionController;

class ListController extends AbstractActionController
{
    /**
     * @var WebMailfacade
     */
    protected $facadeService;

    public function optinAction()
    {
        var_dump($this->getFacadeService()->getQueryURL());
        return new ViewModel(array());
    }

    public function optoutAction()
    {
        return new ViewModel(array());
    }

    public function getFacadeService()
    {
        if ($this->facadeService === null) {
            $this->facadeService = $this->getServiceLocator()->get('playgroundemailcampaign_facade_service');
        }
        return $this->facadeService;
    }

    public function setFacadeService(WebMailfacade $facadeService)
    {
        $this->facadeService = $facadeService;
        return $this;
    }

}