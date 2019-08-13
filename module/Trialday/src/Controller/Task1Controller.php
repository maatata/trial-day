<?php
namespace Trialday\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Trialday\Model\Task1;

class Task1Controller extends AbstractActionController
{
    private $task1Model;

    public function __construct()
    {
        $this->task1Model = new Task1();
    }

    public function indexAction()
    {       
    }

    public function aAction()
    {
        return new ViewModel([
            'result' => $this->task1Model->getResultA(),
        ]);  
    }

    public function bAction()
    {
        return new ViewModel([
            'result' => $this->task1Model->getResultB(),
        ]);  
    }

    public function cAction()
    {
        return new ViewModel([
            'result' => $this->task1Model->getResultC(),
        ]);  
    }
}