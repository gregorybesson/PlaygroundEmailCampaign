<?php

namespace PlaygroundEmailCampaign\Form\Admin;

use Zend\Form\Form;
use Zend\Form\Element;
use ZfcBase\Form\ProvidesEventsForm;
use Zend\Mvc\I18n\Translator;
use Zend\ServiceManager\ServiceManager;
use PlaygroundCore\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use PlaygroundEmailCampaign\Form\Admin\SubscriptionFieldset;

class MailingList extends ProvidesEventsForm
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

        $subsrciptionFieldset = new SubscriptionFieldset(null, $sm, $translator);
        $this->add(array(
            'type'    => 'Zend\Form\Element\Collection',
            'name'    => 'subscriptions',
            'options' => array(
                'id'    => 'subscriptions',
                'label' => $translator->translate('List of contacts', 'playgroundemailcampaign'),
                'count' => 0,
                'allow_add' => true,
                'allow_remove' => true,
                'should_create_template' => true,
                'target_element' => $subsrciptionFieldset,
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