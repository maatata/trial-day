<?php
namespace Trialday\Form;

use Zend\Form\Form;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Adapter\Adapter;

class StudentForm extends Form
{   
    private $classes;

    public function __construct($classes = array())
    {
        $this->classes = $classes;

        // We will ignore the name provided to the constructor
        parent::__construct('student');

        $this->add([
            'name' => 'id',
            'type' => 'hidden',
        ]);
        $this->add([
            'name' => 'firstname',
            'type' => 'text',
            'options' => [
                'label' => 'First Name',
            ],
        ]);
        $this->add([
            'name' => 'lastname',
            'type' => 'text',
            'options' => [
                'label' => 'Last Name',
            ],
        ]);
        $this->add([
            'name' => 'class_id',
            'type' => 'select',
            'options' => [
                'label' => 'Class',
                'empty_option' => 'Please select a Class',
                'value_options' => $this->classes,
            ],
        ]);
        $this->add([
            'name' => 'grade',
            'type' => 'number',
            'options' => [
                'label' => 'Grade',
            ],
            'attributes' => [
                'min' => '0',
                'max' => '100',
                'step' => '0.01', // default step interval is 1
            ],
        ]);
        $this->add([
            'name' => 'submit',
            'type' => 'submit',
            'attributes' => [
                'value' => 'Save',
                'id'    => 'submitbutton',
            ],
        ]);
    }
}