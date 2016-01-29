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
    'bjyauthorize' => array(
    
        'resource_providers' => array(
            'BjyAuthorize\Provider\Resource\Config' => array(
                'email-campaign'      => array(),
            ),
        ),
    
        'rule_providers' => array(
            'BjyAuthorize\Provider\Rule\Config' => array(
                'allow' => array(
                    array(array('admin'), 'email-campaign',       array('list','add','edit','delete')),
                ),
            ),
        ),
    
        'guards' => array(
            'BjyAuthorize\Guard\Controller' => array(
                // Admin area
                array('controller' => 'playgroundemailcampaign_admin_template', 'roles' => array('admin')),
                array('controller' => 'playgroundemailcampaign_admin_list', 'roles' => array('admin')),
                array('controller' => 'playgroundemailcampaign_admin_campaign', 'roles' => array('admin')),
                array('controller' => 'playgroundemailcampaign_list', 'roles' => array('guest', 'user')),
            ),
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

    'view_manager' => array(
        'template_map' => array(),
        'template_path_stack' => array(
            __DIR__ . '/../views/admin',
            __DIR__ . '/../views/frontend'
        ),
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
                                            'route' => '/subscribe/:listId/:contactId',
                                            'constraints' => array (
                                                ':listId' => '[0-9]+',
                                                ':contactId' => '[0-9]+',
                                            ),
                                            'defaults' => array(
                                                'controller' => 'playgroundemailcampaign_admin_list',
                                                'action' => 'subscribe',
                                            ),
                                        ),
                                    ),
                                    'unsubscribe' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/unsubscribe/:listId/:contactId[/:remove]',
                                            'constraints' => array (
                                                ':listId' => '[0-9]+',
                                                ':contactId' => '[0-9]+',
                                                ':remove' => '[0,1]{1}'
                                            ),
                                            'defaults' => array(
                                                'controller' => 'playgroundemailcampaign_admin_list',
                                                'action' => 'unsubscribe',
                                            ),
                                        ),
                                    ),
                                ),
                            ),

                            'contacts-book' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/contacts',
                                    'defaults' => array(
                                        'controller' => 'playgroundemailcampaign_admin_list',
                                        'action' => 'listContacts',
                                    ),
                                ),
                                'may_terminate' => true,
                                'child_routes' => array(
                                    'refresh' => array(
                                        'type' => 'Literal',
                                        'options' => array(
                                            'route' => '/refresh',
                                            'defaults' => array(
                                                'controller' => 'playgroundemailcampaign_admin_list',
                                                'action' => 'initContactBook',
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
                                    'replicate' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/replicate/:campaignId',
                                            'constraints' => array(
                                                ':campaignId' => '[0-9]+',
                                            ),
                                            'defaults' => array(
                                                'controller' => 'playgroundemailcampaign_admin_campaign',
                                                'action' => 'replicate',
                                            ),
                                        ),
                                    ),
                                    'send' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/send/:campaignId',
                                            'constraints' => array(
                                                ':campaignId' => '[0-9]+',
                                            ),
                                            'defaults' => array(
                                                'controller' => 'playgroundemailcampaign_admin_campaign',
                                                'action' => 'send',
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
                'resource' => 'email-campaign',
                'privilege' => 'list',
                'pages' => array(
                    'list-templates' => array(
                        'label' => 'Templates',
                        'route' => 'admin/email-campaign/templates',
                        'resource' => 'email-campaign',
                        'privilege' => 'list',
                    ),
                    'list-lists' => array(
                        'label' => 'Lists',
                        'route' => 'admin/email-campaign/lists',
                        'resource' => 'email-campaign',
                        'privilege' => 'list',
                    ),
                    'list-campaigns' => array(
                        'label' => 'Campaigns',
                        'route' => 'admin/email-campaign/campaigns',
                        'resource' => 'email-campaign',
                        'privilege' => 'list',
                    ),
//                     'list-contacts' => array(
//                         'label' => 'Contacts',
//                         'route' => 'admin/email-campaign/contacts',
//                         'resource' => 'email-campaign',
//                         'privilege' => 'list',
//                     ),
//                     'tracking-data' => array(
//                         'label' => 'Tracking',
//                         'route' => 'admin/email-campaign/tracking',
//                         'resource' => 'email-campaign',
//                         'privilege' => 'list',
//                     ),
                ),
            ),
        ),
    ),
);