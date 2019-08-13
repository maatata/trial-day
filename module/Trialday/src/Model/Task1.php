<?php
// module/Trialday/src/Model/Task1.php:
namespace Trialday\Model;

class Task1
{
    public $merged = array();

    public function getTextFile()
    {
        return file('public/task1_files/file1.txt', FILE_IGNORE_NEW_LINES);
    }

    public function getCsvFile()
    {
        $csv = array_map('str_getcsv', file('public/task1_files/file2.csv'));

        $merged_rows = array();
        foreach($csv AS $row)
            $merged_rows = array_merge($merged_rows, $row);

        return $merged_rows;
    }
    
    public function getMergedFiles()
    {
        $text = $this->getTextFile();
        $csv = $this->getCsvFile();

        $this->merged = array_merge($text, $csv);
        
        return $this->merged;
    }

    public function getResultA()
    {
        $this->getMergedFiles();

        return $this->merged;
    }

    public function getResultB()
    {
        $this->getMergedFiles();
        sort($this->merged);

        return $this->merged;
    }

    public function getResultC()
    {
        $text = $this->getTextFile();
        $csv = $this->getCsvFile();

        $intersect = array_intersect($text, $csv);

        return $intersect;
    }
}