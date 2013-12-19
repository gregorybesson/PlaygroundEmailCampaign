<?php
return array(
    'doctrine' => array(
        'driver' => array(
            'playgroundemailcampaign_entity' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => __DIR__ . '/../src/PlaygroundEmailCampaign/Entity'
            ),
            'orm_default' => array(
                'drivers' => array(
                    'PlaygroundEmailCampaign\Entity' => 'playgroundemailcampaign_entity'
                )
            )
        )
    ),
    'view_manager' => array(
        'template_map' => array(),
        'template_path_stack' => array(
            __DIR__ . '/../views/admin',
            __DIR__ . '/../views/frontend'
        ),
    ),
    'translator' => array(
        'locale' => 'fr_FR',
        'translation_file_patterns' => array(
            array(
                'type' => 'phpArray',
                'base_dir' => __DIR__ . '/../language',
                'pattern' => '%s.php',
                'text_domain' => 'playgroundemailcampaign'
            )
        )
    ),
    'controllers' => array(
        'invokables' => array(
            'playgroundemailcampaign_admin_template' => 'PlaygroundEmailCampaign\Controller\Admin\TemplateController',
            'playgroundemailcampaign_admin_list' => 'PlaygroundEmailCampaign\Controller\Admin\ListController',
            'playgroundemailcampaign_admin_campaign' => 'PlaygroundEmailCampaign\Controller\Admin\CampaignController',
            'playgroundemailcampaign_list' => 'PlaygroundEmailCampaign\Controller\Frontend\ListController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'frontend' => array(
                'child_routes' => array(
                    'optin' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => 'optin',
                            'defaults' => array(
                                'controller' => 'playgroundemailcampaign_list',
                                'action' => 'optin',
                            ),
                        ),
                        'may_terminate' => true,
                    ),
                ),
            ),
            'admin' => array(
                'child_routes' => array(
                    'email-campaign' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/email-campaign',
                            'defaults' => array(
                                'controller' => 'playgroundemailcampaign_admin_template',
                                'action' => 'admin',
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'templates' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/templates',
                                    'defaults' => array(
                                        'controller' => 'playgroundemailcampaign_admin_template',
                                        'action' => 'list',
                                    ),
                                ),
                                'may_terminate' => true,
                                'child_routes' => array(
                                    'add' => array(
                                        'type' => 'Literal',
                                        'options' => array(
                                            'route' => '/add',
                                            'defaults' => array(
                                                'controller' => 'playgroundemailcampaign_admin_template',
                                                'action' => 'add',
                                            ),
                                        ),
                                    ),
                                    'edit' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/edit/:templateId',
                                            'constraints' => array(
                                                ':templateId' => '[0-9]+',
                                            ),
                                            'defaults' => array(
                                                'controller' => 'playgroundemailcampaign_admin_template',
                                                'action' => 'edit',
                                            ),
                                        ),
                                    ),
                                    'remove' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/remove/:templateId',
                                            'constraints' => array(
                                                ':templateId' => '[0-9]+',
                                            ),
                                            'defaults' => array(
                                                'controller' => 'playgroundemailcampaign_admin_template',
                                                'action' => 'remove',
                                            ),
                                        ),
                                    ),
                                ),
                            ),

                            'lists' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/lists',
                                    'defaults' => array(
                                        'controller' => 'playgroundemailcampaign_admin_list',
                                        'action' => 'list',
                                    ),
                                ),
                                'may_terminate' => true,
                                'child_routes' => array(
                                    'add' => array(
                                        'type' => 'Literal',
                                        'options' => array(
                                            'route' => '/add',
                                            'defaults' => array(
                                                'controller' => 'playgroundemailcampaign_admin_list',
                                                'action' => 'add',
                                            ),
                                        ),
                                    ),
                                    'edit' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/edit/:listId',
                                            'constraints' => array(
                                                ':listId' => '[0-9]+',
                                            ),
                                            'defaults' => array(
                                                'controller' => 'playgroundemailcampaign_admin_list',
                                                'action' => 'edit',
                                            ),
                                        ),
                                    ),
                                    'remove' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/remove/:listId',
                                            'constraints' => array(
                                                ':listId' => '[0-9]+',
                                            ),
                                            'defaults' => array(
                                                'controller' => 'playgroundemailcampaign_admin_list',
                                                'action' => 'remove',
                                            ),
                                        ),
                                    ),
                                    'view' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/view[/:listId]',
                                            'constraints' => array(
                                                ':listId' => '[0-9]+',
                                            ),
                                            'defaults' => array(
                                                'controller' => 'playgroundemailcampaign_admin_list',
                                                'action' => 'view',
                                            ),
                                        ),
                                    ),
                                    'subscribe' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/subscribe/:list/:contactId',
                                            'constraints' => array (
                                                ':listId' => '[0-9]+',
                                                ':contactId' => '[0-9]+',
                                            ),
                                            'defaults' => array(
                                                'controller' => 'playgroundemailcampaign_admin_campaign',
                                                'action' => 'subscribe',
                                            ),
                                        ),
                                    ),
                                    'unsubscribe' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/unsubscribe/:list/:contactId',
                                            'constraints' => array (
                                                ':listId' => '[0-9]+',
                                                ':contactId' => '[0-9]+',
                                            ),
                                            'defaults' => array(
                                                'controller' => 'playgroundemailcampaign_admin_campaign',
                                                'action' => 'unsubscribe',
                                            ),
                                        ),
                                    ),
                                ),
                            ),

                            'campaigns' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/campaigns',
                                    'defaults' => array(
                                        'controller' => 'playgroundemailcampaign_admin_campaign',
                                        'action' => 'list',
                                    ),
                                ),
                                'may_terminate' => true,
                                'child_routes' => array(
                                    'add' => array(
                                        'type' => 'Literal',
                                        'options' => array(
                                            'route' => '/add',
                                            'defaults' => array(
                                                'controller' => 'playgroundemailcampaign_admin_campaign',
                                                'action' => 'add',
                                            ),
                                        ),
                                    ),
                                    'edit' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/edit/:campaignId',
                                            'constraints' => array(
                                                ':campaignId' => '[0-9]+',
                                            ),
                                            'defaults' => array(
                                                'controller' => 'playgroundemailcampaign_admin_campaign',
                                                'action' => 'edit',
                                            ),
                                        ),
                                    ),
                                    'remove' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/remove/:campaignId',
                                            'constraints' => array(
                                                ':campaignId' => '[0-9]+',
                                            ),
                                            'defaults' => array(
                                                'controller' => 'playgroundemailcampaign_admin_campaign',
                                                'action' => 'remove',
                                            ),
                                        ),
                                    ),
                                ),

                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'navigation' => array(
        'admin' => array(
            'playgroundemailcampaign' => array(
                'label' => 'Email Campaigns',
                'route' => 'admin/email-campaign',
                'resource' => 'emailcampaign',
                'privilege' => 'list',
                'pages' => array(
                    'list-templates' => array(
                        'label' => 'Templates',
                        'route' => 'admin/email-campaign/templates',
                        'resource' => 'emailcampaign',
                        'privilege' => 'list',
                    ),
                    'list-lists' => array(
                        'label' => 'Lists',
                        'route' => 'admin/email-campaign/lists',
                        'resource' => 'emailcampaign',
                        'privilege' => 'list',
                    ),
                    'list-campaigns' => array(
                        'label' => 'Campaigns',
                        'route' => 'admin/email-campaign/campaigns',
                        'resource' => 'emailcampaign',
                        'privilege' => 'list',
                    ),
//                     'list-contacts' => array(
//                         'label' => 'Contacts',
//                         'route' => 'admin/email-campaign/contacts',
//                         'resource' => 'emailcampaign',
//                         'privilege' => 'list',
//                     ),
//                     'tracking-data' => array(
//                         'label' => 'Tracking',
//                         'route' => 'admin/email-campaign/tracking',
//                         'resource' => 'emailcampaign',
//                         'privilege' => 'list',
//                     ),
                ),
            ),
        ),
    ),
);