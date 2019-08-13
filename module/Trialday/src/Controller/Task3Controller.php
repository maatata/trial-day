<?php
namespace Trialday\Controller;

use Trialday\Model\JobTable;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Trialday\Form\JobForm;
use Trialday\Form\ExportJobForm;
use Trialday\Model\Job;
use Zend\Mail\Message;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Mime;
use Zend\Mime\Part as MimePart;

class Task3Controller extends AbstractActionController
{
    private $table;

    public function __construct(JobTable $table)
    {
        $this->table = $table;
    }

    public function indexAction()
    {
        return new ViewModel([
            'jobs' => $this->table->fetchAll(),
        ]);        
    }

    public function addAction()
    {
        $form = new JobForm();
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();

        if (! $request->isPost()) {
            return ['form' => $form];
        }

        $job = new Job();
        $form->setInputFilter($job->getInputFilter());
        $form->setData($request->getPost());

        if (! $form->isValid()) {
            return ['form' => $form];
        }

        $job->exchangeArray($form->getData());
        $this->table->saveJob($job);
        return $this->redirect()->toRoute('task3');
    }

    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);

        if (0 === $id) {
            return $this->redirect()->toRoute('task3', ['action' => 'add']);
        }

        // Retrieve the job with the specified id. Doing so raises
        // an exception if the job is not found, which should result
        // in redirecting to the landing page.
        try {
            $job = $this->table->getJob($id);
        } catch (\Exception $e) {
            return $this->redirect()->toRoute('task3', ['action' => 'index']);
        }

        $form = new JobForm();
        $form->bind($job);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        $viewData = ['id' => $id, 'form' => $form];

        if (! $request->isPost()) {
            return $viewData;
        }

        $form->setInputFilter($job->getInputFilter());
        $form->setData($request->getPost());

        if (! $form->isValid()) {
            return $viewData;
        }

        $this->table->saveJob($job);

        // Redirect to job list
        return $this->redirect()->toRoute('task3', ['action' => 'index']);
    }

    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('task3');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->table->deleteJob($id);
            }

            // Redirect to job list
            return $this->redirect()->toRoute('task3');
        }

        return [
            'id'    => $id,
            'job' => $this->table->getJob($id),
        ];
    }

    public function exportAction()
    {
        $form = new ExportJobForm();
        $form->get('submit')->setValue('Export');

        $request = $this->getRequest();

        if (! $request->isPost()) {
            return ['form' => $form];
        }

        $jobs = strpos($this->params()->fromPost('type', 'csv'), 'limited') === false ? $this->table->fetchAll()->toArray() : $this->table->fetchLimited()->toArray();

        if (count($jobs) > 0) {
            $docTitle = "jobs";
            $docDate = date('d-m-Y H:i');
            $docExt = (strpos($this->params()->fromPost('type', 'csv'), 'xml') === false ? 'csv' : 'xml');
            $docName = $docTitle . $docDate . '.' . $docExt;
            $docPath = '/tmp/'.$docName;
            

            if(strpos($this->params()->fromPost('type', 'csv'), 'xml') === false) {
                $file = fopen($docPath,"w");

                fputcsv($file,array('id','name', 'description', 'company'));
                
                foreach ($jobs as $job)                
                  fputcsv($file,$job);
                

                fclose($file);
            } else {
                $job_records = array('jobs' => array('job' => $jobs));

                $config = new \Zend\Config\Config($job_records);

                $writer = new \Zend\Config\Writer\Xml();
                $writer->toFile($docPath, $config);
            }

            $body = new MimeMessage();

            $text           = new MimePart('Please find the attached exported jobs file.');
            $text->type     = Mime::TYPE_TEXT;
            $text->charset  = 'utf-8';
            $text->encoding = Mime::ENCODING_QUOTEDPRINTABLE;

            $content = new MimeMessage();
            // This order is important for email clients to properly display the correct version of the content
            $content->setParts([$text]);

            $contentPart = new MimePart($content->generateMessage());

            $attachment              = new MimePart(fopen($docPath, 'r'));
            $attachment->type        = 'text/'.$docExt;
            $attachment->filename    = $docName;
            $attachment->disposition = Mime::DISPOSITION_ATTACHMENT;
            $attachment->encoding    = Mime::ENCODING_BASE64;

            $body = new MimeMessage();
            $body->setParts([$contentPart, $attachment]);

            $message = new Message();
            $message->setBody($body);
            $message->setFrom("test@test.com", "Trial Day")
                    ->addTo($this->params()->fromPost('email'))
                    ->setSubject("Trial Day Development Task 3");

            $contentTypeHeader = $message->getHeaders()->get('Content-Type');
            $contentTypeHeader->setType('multipart/related');

            $transport = new \Zend\Mail\Transport\Sendmail();
            $transport->send($message);      
        }
        
        return $this->redirect()->toRoute('task3');
    }
}