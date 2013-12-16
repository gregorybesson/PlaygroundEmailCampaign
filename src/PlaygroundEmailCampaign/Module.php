<?php

namespace PlaygroundEmailCampaign;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Validator\AbstractValidator;
use Doctrine\ORM\Mapping\Entity;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $sm = $e->getApplication()->getServiceManager();

        $options = $sm->get('playgroundcore_module_options');
        $locale = $options->getLocale();
        $translator = $sm->get('translator');
        if (!empty($locale)) {
            //translator
            $translator->setLocale($locale);

            // plugins
            $translate = $sm->get('viewhelpermanager')->get('translate');
            $translate->getTranslator()->setLocale($locale);
        }
        AbstractValidator::setDefaultTranslator($translator,'playgroundcore');

        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        // Here we need to schedule the core cron service

        // If cron is called, the $e->getRequest()->getPost() produces an error so I protect it with
        // this test
        if ((get_class($e->getRequest()) == 'Zend\Console\Request')) {
            return;
        }
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoLoader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__.'/../../src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/../../config/module.config.php';
    }


    public function getServiceConfig()
    {
        return array(
            'aliases' => array(
            ),

            'invokables' => array(
                'playgroundemailcampaign_template_service' => 'PlaygroundEmailCampaign\Service\Template',
                'playgroundemailcampaign_mailinglist_service' => 'PlaygroundEmailCampaign\Service\MailingList',
                'playgroundemailcampaign_campaign_service' => 'PlaygroundEmailCampaign\Service\Campaign',
                'playgroundemailcampaign_contact_service' => 'PlaygroundEmailCampaign\Service\Contact',

                'playgroundemailcampaign_mailchimp_service' => 'PlaygroundEmailCampaign\Service\MailChimpService',
            ),

            'factories' => array(
                'playgroundemailcampaign_module_options' => function ($sm) {
                    $config = $sm->get('Configuration');
                    return new Options\ModuleOptions(isset($config['playgroundemailcampaign']) ? $config['playgroundemailcampaign'] : array());
                },
                'playgroundemailcampaign_template_mapper' => function ($sm) {
                    $mapper = new Mapper\Template(
                        $sm->get('doctrine.entitymanager.orm_default')
                    );
                    return $mapper;
                },
                'playgroundemailcampaign_contact_mapper' => function ($sm) {
                    $mapper = new Mapper\Contact(
                        $sm->get('doctrine.entitymanager.orm_default')
                    );
                    return $mapper;
                },
                'playgroundemailcampaign_mailinglist_mapper' => function ($sm) {
                    $mapper = new Mapper\MailingList(
                        $sm->get('doctrine.entitymanager.orm_default')
                    );
                    return $mapper;
                },
                'playgroundemailcampaign_subscription_mapper' => function ($sm) {
                    $mapper = new Mapper\Subscription(
                        $sm->get('doctrine.entitymanager.orm_default')
                    );
                    return $mapper;
                },
                'playgroundemailcampaign_campaign_mapper' => function ($sm) {
                    $mapper = new Mapper\Campaign(
                        $sm->get('doctrine.entitymanager.orm_default')
                    );
                    return $mapper;
                },
                'playgroundemailcampaign_email_mapper' => function ($sm) {
                    $mapper = new Mapper\Email(
                        $sm->get('doctrine.entitymanager.orm_default')
                    );
                    return $mapper;
                },
                'playgroundemailcampaign_template_form' => function  ($sm) {
                    $translator = $sm->get('translator');
                    $form = new Form\Admin\Template(null, $sm, $translator);
                    $template = new \PlaygroundEmailCampaign\Entity\Template();
                    $form->setInputFilter($template->getInputFilter());
                    return $form;
                },
                'playgroundemailcampaign_mailinglist_form' => function  ($sm) {
                    $translator = $sm->get('translator');
                    $form = new Form\Admin\MailingList(null, $sm, $translator);
//                     $template = new \PlaygroundEmailCampaign\Entity\MailingList();
//                     $form->setInputFilter($template->getInputFilter());
                    return $form;
                },
                'playgroundemailcampaign_campaign_form' => function  ($sm) {
                    $translator = $sm->get('translator');
                    $form = new Form\Admin\Campaign(null, $sm, $translator);
                    $campaign = new \PlaygroundEmailCampaign\Entity\Campaign();
                    $form->setInputFilter($campaign->getInputFilter());
                    return $form;
                },
            ),
        );
    }
}