<?php

use PHPUnit\Framework\TestCase;

class UrlModelTest extends TestCase
{
    /**
     * @var \Propeller\Models\UrlModel
     */
    private $urlModel;

    public function setUp()
    {
        $this->urlModel = new \Propeller\Models\UrlModel();
    }

    public function testMainRouteWithTable()
    {
        $this->urlModel->base = 'foo';

        $this->assertEquals('foo/bar', $this->urlModel->main('bar'));
    }

    public function testMainRouteWithTableAndKey()
    {
        $this->urlModel->base = 'foo';

        $this->assertEquals('foo/bar/baz', $this->urlModel->main('bar', 'baz'));
    }

    public function testGetFirstSegment()
    {
        $_SERVER['REQUEST_URI'] = '/foo/bar/baz';

        $this->assertEquals('foo', $this->urlModel->getSegment());
    }

    public function testGetSecondSegment()
    {
        $_SERVER['REQUEST_URI'] = '/foo/bar/baz';

        $this->assertEquals('bar', $this->urlModel->getSegment(2));
    }

    public function testGetLastSegment()
    {
        $_SERVER['REQUEST_URI'] = '/foo/bar/baz';

        $this->assertEquals('baz', $this->urlModel->getSegment(-1));
    }

    public function testGetUrl()
    {
        $_SERVER['REQUEST_URI'] = '/';

        $this->assertEquals('/', $this->urlModel->getUrl());
    }
}
