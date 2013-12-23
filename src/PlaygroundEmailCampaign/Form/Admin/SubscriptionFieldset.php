<?php

namespace PlaygroundEmailCampaign\Form\Admin;

use Zend\Form\Fieldset;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;
use PlaygroundCore\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\I18n\Translator\Translator;
use Zend\ServiceManager\ServiceManager;

class SubscriptionFieldset extends Fieldset
{
    protected $serviceManager;

    public function __construct($name = null, ServiceManager $serviceManager, Translator $translator)
    {
        parent::__construct($name);

        $this->setServiceManager($serviceManager);

        $entityManager = $serviceManager->get('doctrine.entitymanager.orm_default');

        $this->setHydrator(new DoctrineHydrator($entityManager, 'PlaygroundEmailCampaign\Entity\Subscription'))
        ->setObject(new \PlaygroundEmailCampaign\Entity\Subscription());

        $this->add(array(
            'name' => 'id',
            'type'  => 'Zend\Form\Element\Hidden',
            'attributes' => array(
                'value' => 0,
            ),
        ));

        $this->add(array(
            'name' => 'mailingList',
            'type'  => 'Zend\Form\Element\Hidden',
            'attributes' => array(
                'value' => 0,
            ),
        ));

        $contacts = $this->getActiveContacts();
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'contact',
            'options' => array(
                'empty_option' => $translator->translate('Sélectionner un contact', 'playgroundemailcampaign'),
                'value_options' => $contacts,
            )
        ));


        $this->add(array(
            'type' => 'Zend\Form\Element\Button',
            'name' => 'remove',
            'options' => array(
                'label' => $translator->translate('Supprimer', 'playgroundemailcampaign'),
            ),
            'attributes' => array(
                'class' => 'delete-button',
            )
        ));
    }

    public function getActiveContacts()
    {
        $contacts = array();
        $contactMapper = $this->getServiceManager()->get('playgroundemailcampaign_contact_service')->getContactMapper();
        $results = $contactMapper->findBy(array("optin"=>true));
        foreach ($results as $result) {
            $contacts[$result->getId()] = $result->getUser()->getEmail();
        }

        return $contacts;
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
     */
    public function setServiceManager (ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;

        return $this;
    }
}