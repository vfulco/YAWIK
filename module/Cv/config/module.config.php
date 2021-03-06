<?php
return array(
    
    'doctrine' => array(
        'driver' => array(
            'odm_default' => array(
                'drivers' => array(
                    'Cv\Entity' => 'annotation',
                ),
            ),
            'annotation' => array(
                /*
                 * All drivers (except DriverChain) require paths to work on. You
                 * may set this value as a string (for a single path) or an array
                 * for multiple paths.
                 * example https://github.com/doctrine/DoctrineORMModule
                 */
                'paths' => array( __DIR__ . '/../src/Cv/Entity'),
            ),
        ),
    ),
    
    
    
    // Translations
    'translator' => array(
        'translation_file_patterns' => array(
            array(
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ),
        ),
    ),
    
    // Routes
    /* DISABLED until module is fixed */
    // TODO: Remove comments when module is fixed.
//    'router' => array(
//        'routes' => array(
//            'lang' => array(
//                'child_routes' => array(
//                    'cvs' => array(
//                        'type' => 'Literal',
//                        'options' => array(
//                            'route'    => '/cvs',
//                            'defaults' => array(
//                                'controller' => 'Cv\Controller\Index',
//                                'action'     => 'index',
//                            ),
//                        ),
//                        'may_terminate' => true,
//                        'child_routes' => array(
//                            'create' => array(
//                                'type' => 'Literal',
//                                'options' => array(
//                                    'route' => '/create',
//                                    'defaults' => array(
//                                        'controller' => 'Cv\Controller\Manage',
//                                        'action' => 'form',
//                                        'id' => false,
//                                    ),
//                                ),
//                                'may_terminate' => true,
//                            ),
//                            'save' => array(
//                                'type' => 'Literal',
//                                'options' => array(
//                                    'route' => '/save',
//                                    'defaults' => array(
//                                        'controller' => 'Cv\Controller\Manage',
//                                        'action' => 'save',
//                                    ),
//                                ),
//                                'may_terminate' => true,
//                            ),
//                        ),
//                    ),
//                ),
//            ),
//        ),
//    ),
    
    'acl' => array(
        'rules' => array(
            'user' => array(
                'allow' => array(
                    'route/lang/cvs',
                    'Cv\Controller\Manage',
                ),
            ),
        ),
    ),
    
    // Configuration of the controller service manager (Which loads controllers)
    'controllers' => array(
        'invokables' => array(
            'Cv\Controller\Index' => 'Cv\Controller\IndexController',
            'Cv\Controller\Manage' => 'Cv\Controller\ManageController',
        ),
    ),
    
    // Navigation
    // Disabled until module is fixed
    // TODO: Remove comments when module is fixed
//    'navigation' => array(
//        'default' => array(
//            'resume' => array(
//                'label' =>  /*@translate*/ 'Resumes',
//                'route' => 'lang/cvs',
//                'resource' => 'route/lang/cvs',
//                'order' => 10,
//                'pages' => array(
//                    'list' => array(
//                        'label' => /*@translate*/ 'Overview',
//                        'route' => 'lang/cvs',
//                    ),
//                    'create' => array(
//                        'label' => /*@translate*/ 'Create resume',
//                        'route' => 'lang/cvs/create',
//                    ),
//                ),
//            ),
//        ),
//    ),
    
    'view_manager' => array(
        
    
        // Map template to files. Speeds up the lookup through the template stack.
        'template_map' => array(
            //'form/div-wrapper-fieldset' => __DIR__ . '/../view/form/div-wrapper-fieldset.phtml',
        ),
    
        // Where to look for view templates not mapped above
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
    'filters' => array(
        'factories' => array(
            'Cv/PaginationQuery' => 'Cv\Repository\Filter\PaginationQueryFactory',
            'Cv/JsonPaginationQuery' => 'Cv\Repository\Filter\JsonPaginationQueryFactory',
        ),
    ),
    
    'form_elements' => array(
        'invokables' => array(
            'CvForm'            => '\Cv\Form\Cv',
            'CvFieldset'        => '\Cv\Form\CvFieldset',
            'EducationFieldset' => '\Cv\Form\EducationFieldset',
            'EmploymentFieldset' => '\Cv\Form\EmploymentFieldset',
        	'SkillFieldset' => '\Cv\Form\SkillFieldset',
        	'NativeLanguageFieldset' => '\Cv\Form\NativeLanguageFieldset',
        	'LanguageSkillFieldset' => '\Cv\Form\LanguageFieldset',
        		
            
        ),
        'factories' => array(
            'Cv' => '\Cv\Form\CvFactory',
            'EducationCollection' => '\Cv\Form\EducationCollectionFactory',
        ),
    ),
    
);