<?php

class ESys_PHPUnit_TestSuiteBuilder {


    public function build ($suiteClass, $targetDirectory)
    {
        $suite = new $suiteClass();
        $suite->setName($suiteClass);
        $testFiles = $this->findTestFiles($targetDirectory, true);
        foreach ($testFiles as $testFile) {
            require_once $testFile;
        }
        $classNameCalculator = 
            new ESys_PHPUnit_TestSuiteBuilder_ClassNameCalculator($suiteClass, $targetDirectory);
        foreach ($testFiles as $testFile) {
            $className = $classNameCalculator->className($testFile);
            if (substr($className, 0 - strlen('Suite')) == 'Suite') {
                $suite->addTestSuite(call_user_func(array($className, 'suite')));
            } else {
                $suite->addTestSuite($className);
            }
        }
        return $suite;
    }


    private function findTestFiles ($targetDirectory, $skipSuiteSearch = false)
    {
        if (! $skipSuiteSearch) {
            $suiteList = glob($targetDirectory.'/*Suite.php');
            if (count($suiteList)) {
                return array($suiteList[0]);
            }
        }
        $fileList = glob($targetDirectory.'/*');
        $testFileList = array();
        foreach ($fileList as $file) {
            if (substr($file, 0 - strlen('Test.php')) == 'Test.php') {
                $testFileList[] = $file;
            } else if (is_dir($file)) {
                $testFileList = array_merge($testFileList, $this->findTestFiles($file));
            }
        }
        return $testFileList;
    }


}


class ESys_PHPUnit_TestSuiteBuilder_ClassNameCalculator {


    private $suiteClass;


    private $targetDirectory;


    private $classRootDirectory;


    public function __construct ($suiteClass, $targetDirectory)
    {
        $this->suiteClass = $suiteClass;
        $this->targetDirectory = $targetDirectory;
    }


    private function classRootDirectory ()
    {
        if (! isset($this->classRootDirectory)) {
            $packageDepth = count(explode('_', $this->suiteClass));
            $this->classRootDirectory = $this->targetDirectory;
            for ($i=0; $i < $packageDepth -1; $i++) {
                $this->classRootDirectory = dirname($this->classRootDirectory);
            }
        }
        return $this->classRootDirectory;
    }


    public function className ($classFile)
    {
        $classRootDirectory = $this->classRootDirectory().'/';
        $className = substr($classFile, strlen($classRootDirectory));
        $className = substr($className, 0, - strlen('.php'));
        $className = str_replace('/', '_', $className);
        return $className;
    }


}
