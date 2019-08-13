<?php
namespace Trialday;

use Zend\Router\Http\Segment;

return [

    'router' => [
        'routes' => [
            'home' => [
                'type' => Segment::class,
                'options' => [
                    'route'    => '/',
                    'defaults' => [
                        'controller' => Controller\Task1Controller::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'task1' => [
                'type'    => Segment::class,
                'options' => [
                    'route' => '/task1[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\Task1Controller::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'task2' => [
                'type'    => Segment::class,
                'options' => [
                    'route' => '/task2[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\Task2Controller::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'task3' => [
                'type'    => Segment::class,
                'options' => [
                    'route' => '/task3[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\Task3Controller::class,
                        'action'     => 'index',
                    ],
                ],
            ],            
        ],
    ],

    'view_manager' => [
        'template_path_stack' => [
            'trialday' => __DIR__ . '/../view',
        ],
    ],
];