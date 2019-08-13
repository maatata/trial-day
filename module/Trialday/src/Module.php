<?php
namespace Trialday;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\ModuleManager\Feature\ConfigProviderInterface;

class Module implements ConfigProviderInterface
{
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function getServiceConfig()
    {
        return [
            'factories' => [
                Model\StudentTable::class => function($container) {
                    $studentTableGateway = $container->get(Model\StudentTableGateway::class);
                    return new Model\StudentTable($studentTableGateway);
                },
                Model\StudentTableGateway::class => function ($container) {
                    $dbAdapter = $container->get(AdapterInterface::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\Student());
                    return new TableGateway('student', $dbAdapter, null, $resultSetPrototype);
                },
                Model\JobTable::class => function($container) {
                    $jobTableGateway = $container->get(Model\JobTableGateway::class); 
                    return new Model\JobTable($jobTableGateway);
                },
                Model\JobTableGateway::class => function ($container) {
                    $dbAdapter = $container->get(AdapterInterface::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\Job());
                    return new TableGateway('job', $dbAdapter, null, $resultSetPrototype);
                },
            ],
        ];
    }

    public function getControllerConfig()
    {
        return [
            'factories' => [
                Controller\Task1Controller::class => function($container) {
                    return new Controller\Task1Controller();
                },
                Controller\Task2Controller::class => function($container) {
                    return new Controller\Task2Controller(
                        $container->get(Model\StudentTable::class)
                    );
                },
                Controller\Task3Controller::class => function($container) {
                    return new Controller\Task3Controller(
                        $container->get(Model\JobTable::class)
                    );
                },
            ],
        ];
    }
}