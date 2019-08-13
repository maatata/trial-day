<?php
namespace Trialday\Model;

use RuntimeException;
use Zend\Db\TableGateway\TableGatewayInterface;

class JobTable
{
    private $jobTableGateway;

    public function __construct(TableGatewayInterface $jobTableGateway)
    {
        $this->jobTableGateway = $jobTableGateway;
    }

    public function fetchAll()
    {
        $sqlSelect = $this->jobTableGateway->getSql()->select();
        $sqlSelect->columns(array('id' => 'id', 'name' => 'name', 'description' => 'description', 'company' => 'company'));
        $resultSet = $this->jobTableGateway->selectWith($sqlSelect);

        return $resultSet;
    }

    public function fetchLimited()
    {
        $sqlSelect = $this->jobTableGateway->getSql()->select();
        $sqlSelect->columns(array('id' => 'id', 'name' => 'name', 'description' => new \Zend\Db\Sql\Expression('IF(CHAR_LENGTH(description) > 100, CONCAT(LEFT(description , 100), " ..."), description)'), 'company' => 'company'));
        $resultSet = $this->jobTableGateway->selectWith($sqlSelect);

        return $resultSet;
    }

    public function getJob($id)
    {
        $id = (int) $id;
        $rowset = $this->jobTableGateway->select(['id' => $id]);
        $row = $rowset->current();
        if (! $row) {
            throw new RuntimeException(sprintf(
                'Could not find row with identifier %d',
                $id
            ));
        }

        return $row;
    }

    public function getJobs() {
        $result = $this->fetchAll();
        $jobs = array();
        foreach($result AS $job)
            $jobs[$job->id] = $job->name;

        return $jobs;
    }

    public function saveJob(Job $job)
    {
        $data = [
            'name' => $job->name,
            'description'  => $job->description,
            'company'  => $job->company,
        ];

        $id = (int) $job->id;

        if ($id === 0) {
            $this->jobTableGateway->insert($data);
            return;
        }

        if (! $this->getJob($id)) {
            throw new RuntimeException(sprintf(
                'Cannot update job with identifier %d; does not exist',
                $id
            ));
        }

        $this->jobTableGateway->update($data, ['id' => $id]);
    }

    public function deleteJob($id)
    {
        $this->jobTableGateway->delete(['id' => (int) $id]);
    }
}