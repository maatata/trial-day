<?php
namespace Trialday\Model;

use RuntimeException;
use Zend\Db\TableGateway\TableGatewayInterface;

class StudentTable
{
    private $studentTableGateway;

    public function __construct(TableGatewayInterface $studentTableGateway)
    {
        $this->studentTableGateway = $studentTableGateway;
    }

    public function fetchAll()
    {
        $sqlSelect = $this->studentTableGateway->getSql()->select();
        $sqlSelect->columns(array('id' => 'id', 'firstname' => 'firstname', 'lastname' => 'lastname', 'class_id' => 'class_id', 'grade' => 'grade'));
        $sqlSelect->order('firstname ASC, lastname ASC, class_id ASC, grade ASC');
        $resultSet = $this->studentTableGateway->selectWith($sqlSelect);

        return $resultSet;
    }

    public function fetchStudentsAverageGrades()
    {
        $sqlSelect = $this->studentTableGateway->getSql()->select();
        $sqlSelect->columns(array('id' => 'id', 'firstname' => 'firstname', 'lastname' => 'lastname', 'class_id' => 'class_id', 'average_grade' => new \Zend\Db\Sql\Expression('AVG(grade)')));
        $sqlSelect->order('firstname ASC, lastname ASC, class_id ASC, average_grade ASC');
        $sqlSelect->group(new \Zend\Db\Sql\Expression('CONCAT_WS(", ", firstname, lastname)'));
        $resultSet = $this->studentTableGateway->selectWith($sqlSelect);

        return $resultSet;
    }

    public function fetchClassAverageGrades()
    {
        $sqlSelect = $this->studentTableGateway->getSql()->select();
        $sqlSelect->columns(array('class_name' => new \Zend\Db\Sql\Expression('CONCAT("Class ", class_id)'), 'average_grade' => new \Zend\Db\Sql\Expression('AVG(grade)')));
        $sqlSelect->order('class_id ASC, average_grade ASC');
        $sqlSelect->group('class_id');
        $resultSet = $this->studentTableGateway->selectWith($sqlSelect);

        return $resultSet;
    }

    public function fetchOverallAverageGrade()
    {
        $sqlSelect = $this->studentTableGateway->getSql()->select();
        $sqlSelect->columns(array('overall_average_grade' => new \Zend\Db\Sql\Expression('AVG(grade)')));
        $resultSet = $this->studentTableGateway->selectWith($sqlSelect);

        return $resultSet->current()->overall_average_grade;
    }

    public function getStudent($id)
    {
        $id = (int) $id;
        $rowset = $this->studentTableGateway->select(['id' => $id]);
        $row = $rowset->current();
        if (! $row) {
            throw new RuntimeException(sprintf(
                'Could not find row with identifier %d',
                $id
            ));
        }

        return $row;
    }

    public function getClasses() {
        $result = $this->fetchAll();
        $classes = array();
        for($i = 1; $i <= 3; $i++) {
            $classes[$i] = 'Class ' . $i;
        }
        
        return $classes;
    }

    public function saveStudent(Student $student)
    {
        $data = [
            'firstname' => $student->firstname,
            'lastname' => $student->lastname,
            'class_id'  => $student->class_id,
            'grade' => $student->grade,
        ];

        $id = (int) $student->id;

        if ($id === 0) {
            $this->studentTableGateway->insert($data);
            return;
        }

        if (! $this->getStudent($id)) {
            throw new RuntimeException(sprintf(
                'Cannot update student with identifier %d; does not exist',
                $id
            ));
        }

        $this->studentTableGateway->update($data, ['id' => $id]);
    }

    public function deleteStudent($id)
    {
        $this->studentTableGateway->delete(['id' => (int) $id]);
    }
}