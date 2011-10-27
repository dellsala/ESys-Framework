<?php

require_once 'PHPUnit/Framework.php';
require_once 'ESys/Data/Record/FileResourceImage.php';

class ESys_Data_Record_FileResourceImageTest extends PHPUnit_Framework_TestCase {


    protected $storageDir; 
    protected $sourceFile; 


    public function setup ()
    {
        $this->originalFile = dirname(__FILE__).'/images/gradient.original.jpg';
        $this->sourceFile = dirname(__FILE__).'/images/gradient.jpg';
        copy($this->originalFile, $this->sourceFile);
        $this->storageDir = dirname(__FILE__).'/storage';
        $this->deleteDirectory($this->storageDir);
        mkdir($this->storageDir);
    }


    public function teardown ()
    {
        if (file_exists($this->sourceFile)) {
            unlink($this->sourceFile);
        }
        $this->deleteDirectory($this->storageDir);
    }


    public function testInstallsImageFile ()
    {
        $imageResource = new ESys_Data_Record_FileResourceImage($this->storageDir);
        $imageResource->install($this->sourceFile);
        $expectedFilePath = $this->storageDir.'/'.basename($this->sourceFile);
        $expectedFilePath = preg_replace('/\.jpg$/', '.raw.jpg', $expectedFilePath);
        $this->assertFileExists($expectedFilePath);
    }


    public function testResizesImageToHeight ()
    {
        $imageResource = $this->createInstalledImageResource();
        $height = 100;

        $expectedSuffix = '.h'.$height.'.jpg';

        $expectedUrl = $this->calculateExpectedUrl($expectedSuffix);
        $expectedFilePath = $this->calculateExpectedFilePath($expectedSuffix);

        $resizedUrl = $imageResource->urlByHeight($height);
        $imageInfo = getimagesize($expectedFilePath);

        $this->assertEquals($expectedUrl, $resizedUrl);
        $this->assertFileExists($expectedFilePath);
        $this->assertEquals($height, $imageInfo[1]);
    }


    public function testResizesImageToWidth ()
    {
        $imageResource = $this->createInstalledImageResource();
        $width = 100;

        $expectedSuffix = '.w'.$width.'.jpg';

        $expectedUrl = $this->calculateExpectedUrl($expectedSuffix);
        $expectedFilePath = $this->calculateExpectedFilePath($expectedSuffix);

        $resizedUrl = $imageResource->urlByWidth($width);
        $imageInfo = getimagesize($expectedFilePath);

        $this->assertEquals($expectedUrl, $resizedUrl);
        $this->assertFileExists($expectedFilePath);
        $this->assertEquals($width, $imageInfo[0]);
    }


    protected function createInstalledImageResource ()
    {
        $imageResource = new ESys_Data_Record_FileResourceImage($this->storageDir);
        $imageResource->install($this->sourceFile);
        return $imageResource;
    }


    protected function calculateExpectedUrl ($expectedSuffix)
    {
        return $this->calculateExpectedPath('/'.basename($this->sourceFile), $expectedSuffix);
    }


    protected function calculateExpectedFilePath ($expectedSuffix)
    {
        return $this->calculateExpectedPath(
            $this->storageDir.'/'.basename($this->sourceFile), $expectedSuffix);
    }


    protected function calculateExpectedPath ($path, $expectedSuffix)
    {
        $originalSuffixPattern = '/\.jpg$/';
        return preg_replace($originalSuffixPattern, $expectedSuffix, $path);
    }


    protected function deleteDirectory ($dir)
    {
        if (! file_exists($dir)) {
            return;
        }
        $directoryIterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir),
            RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($directoryIterator as $item) {
            $item->isDir() ? rmdir($item) : unlink($item);
        }
        rmdir($dir);
    }


}