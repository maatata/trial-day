<?php
namespace Trialday\Form;

use Zend\Form\Form;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Adapter\Adapter;

class ExportJobForm extends Form
{   
    private $counties;

    public function __construct($counties = array())
    {
        $this->counties = $counties;

        // We will ignore the name provided to the constructor
        parent::__construct('exportjob');

        $this->add([
            'name' => 'type',
            'type' => 'select',
            'options' => [
                'label' => 'File Format',
                'empty_option' => 'Please select a File Format',
                'value_options' => array(
                    'csv' => 'CSV',
                    'xml' => 'XML',
                    'xml-limited' => 'XML with limited description'
                )
            ],
        ]);
        $this->add([
            'name' => 'email',
            'type' => 'email',
            'options' => [
                'label' => 'E-mail Address',
            ],
        ]);
        $this->add([
            'name' => 'submit',
            'type' => 'submit',
            'attributes' => [
                'value' => 'Download',
                'id'    => 'submitbutton',
            ],
        ]);
    }
}