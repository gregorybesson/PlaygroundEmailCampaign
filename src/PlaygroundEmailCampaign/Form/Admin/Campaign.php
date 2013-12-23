<?php

namespace PlaygroundEmailCampaign\Form\Admin;

use Zend\Form\Form;
use Zend\Form\Element;
use ZfcBase\Form\ProvidesEventsForm;
use Zend\I18n\Translator\Translator;
use Zend\ServiceManager\ServiceManager;
use PlaygroundCore\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use PlaygroundEmailCampaign\Form\Admin\SubscriptionFieldset;

class Campaign extends ProvidesEventsForm
{
    protected $serviceManager;

    public function __construct ($name = null, ServiceManager $sm, Translator $translator)
    {
        parent::__construct($name);

        $this->setServiceManager($sm);
        $entityManager = $sm->get('doctrine.entitymanager.orm_default');

        $hydrator = new DoctrineHydrator($entityManager, 'PlaygroundEmailCampaign\Entity\MailingList');
        $hydrator->addStrategy('partner', new \PlaygroundCore\Stdlib\Hydrator\Strategy\ObjectStrategy());
        $this->setHydrator($hydrator);

        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype', 'multipart/form-data');

        $this->add(array(
            'name' => 'id',
            'type'  => 'Zend\Form\Element\Hidden',
            'attributes' => array(
                'value' => 0,
            ),
        ));

        $this->add(array(
            'name' => 'name',
            'options' => array(
                'label' => $translator->translate('Name', 'playgroundemailcampaign'),
            ),
            'attributes' => array(
                'type' => 'text',
                'placeholder' => $translator->translate('Name', 'playgroundemailcampaign'),
            ),
        ));

        $this->add(array(
            'type'    => 'Zend\Form\Element\Textarea',
            'name'    => 'description',
            'options' => array(
                'id'    => 'description',
                'label' => $translator->translate('Description', 'playgroundemailcampaign'),
            ),
        ));

        $this->add(array(
            'type'    => 'Zend\Form\Element\Text',
            'name'    => 'fromName',
            'options' => array(
                'id'    => 'fromName',
                'label' => $translator->translate('Sender Name', 'playgroundemailcampaign'),
            ),
        ));

        $this->add(array(
            'type'    => 'Zend\Form\Element\Text',
            'name'    => 'fromEmail',
            'options' => array(
                'id'    => 'fromEmail',
                'label' => $translator->translate('Sender Email', 'playgroundemailcampaign'),
            ),
        ));


        $this->add(array(
            'type'    => 'Zend\Form\Element\Text',
            'name'    => 'subject',
            'options' => array(
                'id'    => 'subject',
                'label' => $translator->translate('Subject', 'playgroundemailcampaign'),
            ),
        ));

        $templates = $this->getTemplates();
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'template',
            'options' => array(
                'empty_option' => $translator->translate('Select a template', 'playgroundemailcampaign'),
                'value_options' => $templates,
                'label' => $translator->translate('Template', 'playgroundemailcampaign')
            )
        ));

        $lists = $this->getLists();
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'mailingList',
            'options' => array(
                'empty_option' => $translator->translate('Select a list', 'playgroundemailcampaign'),
                'value_options' => $lists,
                'label' => $translator->translate('List', 'playgroundemailcampaign')
            )
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'isTracked',
            'attributes' => array(
                'id' => 'isTracked',
            ),
            'options' => array(
                'value_options' => array(
                    '1' => 'Oui',
                    '0' => 'Non'
                ),
                'label' => $translator->translate('Track', 'playgroundemailcampaign')
            )
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Text',
            'name' => 'trackingURL',
            'options' => array(
                'label' => $translator->translate('Tracking URL', 'playgroundemailcampaign')
            )
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Text',
            'name' => 'unsubscribeURL',
            'options' => array(
                'label' => $translator->translate('Unsubscribe URL', 'playgroundemailcampaign')
            )
        ));

        $submitElement = new Element\Button('submit');
        $submitElement->setAttributes(array(
            'type'  => 'submit',
            'class' => 'btn btn-primary',
        ));

        $this->add($submitElement, array(
            'priority' => -100,
        ));
    }

    public function getLists()
    {
        $lists = array();
        $listMapper = $this->getServiceManager()->get('playgroundemailcampaign_mailinglist_mapper');
        $results = $listMapper->findAll();
        foreach ($results as $result) {
            $lists[$result->getId()] = $result->getName();
        }
        return $lists;
    }

    public function getTemplates()
    {
        $templates = array();
        $templateMapper = $this->getServiceManager()->get('playgroundemailcampaign_template_mapper');
        $results = $templateMapper->findAll();
        foreach ($results as $result) {
            $templates[$result->getId()] = $result->getTitle();
        }
        return $templates;
    }

    /**
     * Retrieve service manager instance
     *
     * @return ServiceManager
     */
    public function getServiceManager ()
    {
        return $this->serviceManager;
    }

    /**
     * Set service manager instance
     *
     * @param  ServiceManager $serviceManager
     * @return User
     */
    public function setServiceManager (ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;

        return $this;
    }
}