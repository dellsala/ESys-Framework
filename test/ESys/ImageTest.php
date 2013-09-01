<?php


require_once 'ESys/Image.php';


class ESys_ImageTest extends PHPUnit_Framework_TestCase {


    public function setup ()
    {
        $baseDataDirectory = dirname(__FILE__).'/ImageTest';
        $this->jpegSourceFile = $baseDataDirectory.'/example.jpg';
        $this->gifSourceFile = $baseDataDirectory.'/example.gif';
        $this->pngSourceFile = $baseDataDirectory.'/example.png';
        $this->image100x200File = $baseDataDirectory.'/100x200.jpg';
        $this->tempSaveDirectory = $baseDataDirectory.'/temp';
        if (! file_exists($this->tempSaveDirectory)) {
            mkdir($this->tempSaveDirectory);
        }
        $this->image = new ESys_Image();
    }


    public function tearDown ()
    {
        $this->image->release();
        foreach (glob($this->tempSaveDirectory.'/*') as $tempFile) {
            unlink($tempFile);
        }
        rmdir($this->tempSaveDirectory);
    }


    public function testLoadsJpegFiles ()
    {
        $this->assertImageFileIsLoadable($this->jpegSourceFile);
    }


    public function testLoadsGifFiles ()
    {
        $this->assertImageFileIsLoadable($this->gifSourceFile);
    }


    public function testLoadsPngFiles ()
    {
        $this->assertImageFileIsLoadable($this->pngSourceFile);
    }


    public function testProvidesAspectRatioOfImage ()
    {
        $this->image->load($this->image100x200File);
        $expectedAspectRatio = 0.5;
        $this->assertEquals($expectedAspectRatio, $this->image->aspectRatio());
    }


    public function testProvidesHeightOfImage ()
    {
        $this->image->load($this->image100x200File);
        $expectedHeight = 200;
        $this->assertEquals($expectedHeight, $this->image->height());
    }


    public function testProvidesWidthOfImage ()
    {
        $this->image->load($this->image100x200File);
        $expectedWidth = 100;
        $this->assertEquals($expectedWidth, $this->image->width());
    }


    public function testResizesImage ()
    {
        $this->image->load($this->image100x200File);
        $this->image->resize(50, 50);
        $this->assertEquals(50, $this->image->width());
        $this->assertEquals(50, $this->image->height());
    }


    public function testSavesJpegFiles ()
    {
        $this->image->load($this->image100x200File);
        $file = $this->tempSaveDirectory.'/image.jpg';
        $this->image->saveJpeg($file);
        $this->assertImageFileIsOfExpectedType($file, array(
            'height' => 200,
            'width' => 100,
            'mimeType' => 'image/jpeg',
        ));
    }

    public function testSavesGifFiles ()
    {
        $this->image->load($this->image100x200File);
        $file = $this->tempSaveDirectory.'/image.gif';
        $this->image->saveGif($file);
        $this->assertImageFileIsOfExpectedType($file, array(
            'height' => 200,
            'width' => 100,
            'mimeType' => 'image/gif',
        ));
    }


    public function testSavesPngFiles ()
    {
        $this->image->load($this->image100x200File);
        $file = $this->tempSaveDirectory.'/image.png';
        $this->image->savePng($file);
        $this->assertImageFileIsOfExpectedType($file, array(
            'height' => 200,
            'width' => 100,
            'mimeType' => 'image/png',
        ));
    }


    public function testScalesImageToFixedHeight ()
    {
        $this->image->load($this->image100x200File);
        $this->image->scaleToHeight(100);
        $this->assertEquals(50, $this->image->width());
        $this->assertEquals(100, $this->image->height());
    }


    public function testScalesImageToFixedWidth ()
    {
        $this->image->load($this->image100x200File);
        $this->image->scaleToWidth(50);
        $this->assertEquals(50, $this->image->width());
        $this->assertEquals(100, $this->image->height());
    }


    public function testScalesImageToMinimumDimensions ()
    {
        $this->image->load($this->image100x200File);
        $this->image->scaleToMinHeightAndWidth(50, 50);
        $this->assertEquals(50, $this->image->width());
        $this->assertEquals(100, $this->image->height());
    }


    public function testScalesImageToMaximumDimensions ()
    {
        $this->image->load($this->image100x200File);
        $this->image->scaleToMaxHeightAndWidth(50, 50);
        $this->assertEquals(25, $this->image->width());
        $this->assertEquals(50, $this->image->height());
    }


    protected function assertImageFileIsLoadable ($file)
    {
        try {
            $loadResult = $this->image->load($file);
        } catch (Exception $e) {
            $this->fail($e->getMessage());
        }
        $this->assertTrue($loadResult, 'load method should return true');
    }


    protected function assertImageFileIsOfExpectedType ($file, $typeInfo)
    {
        $image = new ESys_Image();
        $image->load($file);
        $loadResult = $this->image->load($file);
        $this->assertEquals($typeInfo['height'], $image->height());
        $this->assertEquals($typeInfo['width'], $image->width());
        $this->assertEquals($typeInfo['mimeType'], $image->mimeType());
        $image->release();
    }


}