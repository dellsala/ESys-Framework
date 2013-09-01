<?php


require_once 'ESys/Data/Record/FileResource.php';

class ESys_Data_Record_FileResourceTest extends PHPUnit_Framework_TestCase {


    protected $storageDir; 
    protected $sourceFile; 


    public function setup ()
    {
        $this->sourceFile = dirname(__FILE__).'/test-file.txt';
        file_put_contents($this->sourceFile, 'foo bar');
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


    public function testInstallsFile ()
    {
        $fileResource = new ESys_Data_Record_FileResource($this->storageDir);
        $this->assertTrue(
            $fileResource->install($this->sourceFile),
            'Install returns success result.'
        );
        $expectedFileLocation = $this->storageDir.'/'.basename($this->sourceFile);
        $this->assertTrue(
            file_exists($expectedFileLocation),
            'File exists in expected location.'
        );
    }


    public function testInstallsFileWithAlternateName ()
    {
        $fileResource = new ESys_Data_Record_FileResource($this->storageDir);
        $alternateName = 'some-other-name.txt';
        $this->assertTrue(
            $fileResource->install($this->sourceFile, $alternateName),
            'Install returns success result.'
        );
        $expectedFileLocation = $this->storageDir.'/'.$alternateName;
        $this->assertTrue(
            file_exists($expectedFileLocation),
            'File exists in expected location.'
        );
    }


    public function testDetectsIfFileIsInstalled ()
    {
        $fileResource = new ESys_Data_Record_FileResource($this->storageDir);
        $expectedFileLocation = $this->storageDir.'/'.basename($this->sourceFile);
        $this->assertFalse(
            $fileResource->isInstalled(),
            'Reports file not installed.'
        );
        $fileResource->install($this->sourceFile);
        $this->assertTrue(
            $fileResource->isInstalled(),
            'Reports file installed.'
        );
        $fileResource->uninstall();
        $this->assertFalse(
            $fileResource->isInstalled(),
            'Reports file not installed.'
        );
    }


    public function testUninstallsFile ()
    {
        $fileResource = new ESys_Data_Record_FileResource($this->storageDir);
        $expectedFileLocation = $this->storageDir.'/'.basename($this->sourceFile);
        $fileResource->install($this->sourceFile);
        $this->assertTrue(
            $fileResource->uninstall(),
            'Uninstall returns success result.'
        );
        $this->assertFalse(
            file_exists($expectedFileLocation),
            'File does not exist after uninstall.'
        );
    }


    public function testProvidesFileSystemPath ()
    {
        $fileResource = new ESys_Data_Record_FileResource($this->storageDir);
        $expectedFileLocation = $this->storageDir.'/'.basename($this->sourceFile);
        $fileResource->install($this->sourceFile);
        $this->assertEquals($expectedFileLocation, $fileResource->fileSystemPath());
    }


    public function testProvidesUrl ()
    {
        $fileResource = new ESys_Data_Record_FileResource($this->storageDir);
        $fileResource->install($this->sourceFile);
        $expectedUrl = '/'.basename($this->sourceFile);
        $this->assertEquals($expectedUrl, $fileResource->url());
    }


    public function testProvidesUrlWithSpecifiedBase ()
    {
        $baseUrl = '/my/dir';
        $fileResource = new ESys_Data_Record_FileResource($this->storageDir, $baseUrl);
        $fileResource->install($this->sourceFile);
        $expectedUrl = $baseUrl.'/'.basename($this->sourceFile);
        $this->assertEquals($expectedUrl, $fileResource->url());
    }


    public function testManagesDirectoriesToSpecifiedDepth ()
    {
        $managedDirectoryDepth = 1;
        $deepStorageDir = $this->storageDir.'/subdir';
        $fileResource = new ESys_Data_Record_FileResource($deepStorageDir, "", $managedDirectoryDepth);
        $expectedFileLocation = $deepStorageDir.'/'.basename($this->sourceFile);
        
        $this->assertTrue(
            file_exists(dirname(dirname($expectedFileLocation))),
            'Base storage directory exists.'
        );
        $this->assertFalse(
            file_exists(dirname($expectedFileLocation)),
            'Managed subdir does not exist.'
        );
        $fileResource->install($this->sourceFile);
        $this->assertTrue(
            file_exists($expectedFileLocation),
            'Installed file exists inside managed subdir.'
        );
        $fileResource->uninstall();
        $this->assertFalse(
            file_exists(dirname($expectedFileLocation)),
            'Managed subdir does not exist after unintall.'
        );
    }

    public function testProvidesFilename ()
    {
        $fileResource = new ESys_Data_Record_FileResource($this->storageDir);
        $fileResource->install($this->sourceFile);
        $expectedFileName = basename($this->sourceFile);
        $this->assertEquals($expectedFileName, $fileResource->filename());
    }


    protected function createSourceFile ()
    {
        $this->sourceFile = dirname(__FILE__).'/test-file.txt';
        file_put_contents($this->sourceFile, 'foo bar');
    }


    protected function deleteSourceFile ()
    {
        if (file_exists($this->sourceFile)) {
            unlink($this->sourceFile);
        }
    }

    
    protected function deleteDirectory ($dir)
    {
        if (! file_exists($dir)) {
            return;
        }
        $directoryIterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(
                $dir, 
                FilesystemIterator::KEY_AS_PATHNAME | FilesystemIterator::CURRENT_AS_FILEINFO | FilesystemIterator::SKIP_DOTS
            ),
            RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($directoryIterator as $item) {
            $item->isDir() ? rmdir($item) : unlink($item);
        }
        rmdir($dir);
    }



}