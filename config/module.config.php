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

                ),
            ),
            'admin' => array(
                'child_routes' => array(

                ),
            ),
        ),
    ),
);