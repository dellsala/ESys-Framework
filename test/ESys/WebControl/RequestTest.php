<?php

require_once 'PHPUnit/Framework.php';
require_once 'ESys/WebControl/Request.php';


class ESys_WebControl_RequestTest extends PHPUnit_Framework_TestCase {



    public function testInvalidConstructorThrowsErrors ()
    {
        $this->setExpectedException('Exception');
        $request = new ESys_WebControl_Request(array(
            'badProperty' => 'some value',
        ));
    }


    /**
     * @dataProvider baseUrlDataProvider
     */
    public function testBaseUrl ($basePath, $expectedUrl)
    {
        $request = new ESys_WebControl_Request(array(
            'basePath' => $basePath,
            'frontControllerPath' => '/front',
            'controllerPath' => '/controller',
        ));
        $this->assertEquals($expectedUrl, $request->url('base'));
    }


    public function baseUrlDataProvider ()
    {
        return array(
            array(
                '',
                ''
            ),
            array(
                '/',
                ''
            ),
            array(
                'path',
                '/path'
            ),
            array(
                'path/',
                '/path'
            ),
            array(
                '/path/otherpath',
                '/path/otherpath'
            ),
            array(
                'path/otherpath/',
                '/path/otherpath'
            ),
        );
    }


    /**
     * @dataProvider frontControllerDataProvider
     */
    public function testFrontControllerUrl ($basePath, $frontControllerPath, $expectedUrl)
    {
        $request = new ESys_WebControl_Request(array(
            'basePath' => $basePath,
            'frontControllerPath' => $frontControllerPath,
            'controllerPath' => '/controller/path',
        ));
        $this->assertEquals($expectedUrl, $request->url('frontController'));
    }


    public function frontControllerDataProvider ()
    {
        return array(
            array(
                '',
                '/',
                '',
            ),
            array(
                '',
                '',
                '',
            ),
            array(
                '/',
                '',
                '',
            ),
            array(
                '/root',
                '/',
                '/root',
            ),
            array(
                '/root',
                '',
                '/root',
            ),
            array(
                '/root',
                '/path/',
                '/root/path',
            ),
            array(
                '/root',
                '/path',
                '/root/path',
            ),
        );
    }



    /**
     * @dataProvider controllerDataProvider
     */
    public function testControllerUrl ($basePath, $frontControllerPath, $controllerPath, $expectedUrl)
    {
        $request = new ESys_WebControl_Request(array(
            'basePath' => $basePath,
            'frontControllerPath' => $frontControllerPath,
            'controllerPath' => $controllerPath,
        ));
        $this->assertEquals($expectedUrl, $request->url('controller'));
    }


    public function controllerDataProvider ()
    {
        return array(
            array(
                '',
                '',
                '',
                '',
            ),
            array(
                '',
                '/',
                '',
                '',
            ),
            array(
                '',
                '',
                '/',
                '',
            ),
            array(
                '/',
                '/',
                '/',
                '',
            ),
            array(
                '',
                '',
                '/controller',
                '/controller',
            ),
            array(
                '',
                '/front/',
                'controller',
                '/front/controller',
            ),
            array(
                'root',
                '',
                'controller/',
                '/root/controller',
            ),
            array(
                'root',
                'front',
                'controller/',
                '/root/front/controller',
            ),
        );
    }


    public function testInvalidUrlType ()
    {
        $this->setExpectedException('Exception');
        $request = new ESys_WebControl_Request();
        $request->url('someInvalidType');
    }


}