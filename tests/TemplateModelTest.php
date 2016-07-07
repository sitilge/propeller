<?php

use PHPUnit\Framework\TestCase;

class TemplateModelTest extends TestCase
{
    /**
     * @var \Propeller\Models\TemplateModel
     */
    private $templateModel;

    public function setUp()
    {
        $this->templateModel = new \Propeller\Models\TemplateModel();
    }

    public function testFileWithoutExtension()
    {
        $this->templateModel->file = '/foo/bar/baz';

        $this->assertAttributeEquals('/foo.php', 'file', $this->templateModel->file('/foo'));
    }

    public function testFileWithExtension()
    {
        $this->templateModel->file = '/foo/bar/baz';

        $this->assertAttributeEquals('/foo.html', 'file', $this->templateModel->file('/foo.html'));
    }

    public function testSetWithString()
    {
        $this->assertAttributeEquals(
            [
                'foo' => 'bar',
            ],
            'data',
            $this->templateModel->set('foo', 'bar'));
    }

    public function testSetWithArray()
    {
        $this->assertAttributeEquals(
            [
                'foo' => 'bar',
                'baz' => 'qux',
            ],
            'data',
            $this->templateModel->set(
                [
                    'foo' => 'bar',
                    'baz' => 'qux',
                ]
            ));
    }

    public function testRenderWithoutInput()
    {
        $root = \org\bovigo\vfs\vfsStream::setup('root');
        \org\bovigo\vfs\vfsStream::newFile('foo.php')->at($root)->setContent('foo bar');
        $this->templateModel->file = $root->getChild('foo.php')->url();

        $this->assertEquals('foo bar', $this->templateModel->render());
    }

    public function testRenderWithInput()
    {
        $this->assertEquals('foo bar', $this->templateModel->render('foo bar'));
    }
}
