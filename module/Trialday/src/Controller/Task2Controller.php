<?php
namespace Trialday\Controller;

use Trialday\Model\StudentTable;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Trialday\Form\StudentForm;
use Trialday\Model\Student;

class Task2Controller extends AbstractActionController
{
    private $studentTable;

    public function __construct(StudentTable $studentTable)
    {
        $this->studentTable = $studentTable;
    }

    public function indexAction()
    {
        return new ViewModel([
            'students' => $this->studentTable->fetchAll(),
        ]);        
    }

    public function addAction()
    {
        $classes = $this->studentTable->getClasses();
        $form = new StudentForm($classes);
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        

        if (! $request->isPost()) {
            return ['form' => $form];
        }


        $student = new Student();
        $form->setInputFilter($student->getInputFilter());
        $form->setData($request->getPost());

        if (! $form->isValid()) {
            return ['form' => $form];
        }

        $student->exchangeArray($form->getData());
        $this->studentTable->saveStudent($student);
        return $this->redirect()->toRoute('task2');
    }

    public function editAction()
    {
        $classes = $this->studentTable->getClasses();
        $id = (int) $this->params()->fromRoute('id', 0);

        if (0 === $id) {
            return $this->redirect()->toRoute('task2', ['action' => 'add']);
        }

        // Retrieve the student with the specified id. Doing so raises
        // an exception if the student is not found, which should result
        // in redirecting to the landing page.
        try {
            $student = $this->studentTable->getStudent($id);
        } catch (\Exception $e) {
            return $this->redirect()->toRoute('task2', ['action' => 'index']);
        }

        $form = new StudentForm($classes);
        $form->bind($student);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        $viewData = ['id' => $id, 'form' => $form];

        if (! $request->isPost()) {
            return $viewData;
        }

        $form->setInputFilter($student->getInputFilter());
        $form->setData($request->getPost());

        if (! $form->isValid()) {
            return $viewData;
        }

        $this->studentTable->saveStudent($student);

        // Redirect to student list
        return $this->redirect()->toRoute('task2', ['action' => 'index']);
    }

    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('task2');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->studentTable->deleteStudent($id);
            }

            // Redirect to state list
            return $this->redirect()->toRoute('task2');
        }

        return [
            'id'    => $id,
            'student' => $this->studentTable->getStudent($id),
        ];
    }

    public function studentAverageGradeAction()
    {
        return new ViewModel([
            'students' => $this->studentTable->fetchStudentsAverageGrades(),
        ]);        
    }

    public function classAverageGradeAction()
    {
        return new ViewModel([
            'classes' => $this->studentTable->fetchClassAverageGrades(),
        ]);        
    }

    public function overallAverageGradeAction()
    {
        return new ViewModel([
            'overall_average_grade' => $this->studentTable->fetchOverallAverageGrade(),
        ]);        
    }

}