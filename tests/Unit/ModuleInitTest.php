<?php

namespace demo;

use AspectMock\Test as AspectMock;
use Mockery as m;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;

class ModuleInitTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var vfsStreamDirectory
     */
    private $vfsRoot;

    public function setUp()
    {
        parent::setUp();

        $this->vfsRoot = vfsStream::setup('outputDir');
    }

    protected function tearDown()
    {
        // remove all registered test doubles
        AspectMock::clean();
    }

    private function createFile($fileName)
    {
        return vfsStream::newFile($fileName)->setContent($content = 'Some content here');
    }

    public function testModuleInit()
    {
        $this->vfsRoot->addChild($this->createFile('file1'));
        $this->vfsRoot->addChild($this->createFile('file1'));
        $this->vfsRoot->addChild(vfsStream::newDirectory('dir1'));

        AspectMock::double(
            \Codeception\Configuration::class,
            [ 'outputDir' => vfsStream::url('outputDir') ]
        );

        // Cleansman needs no configuration atm
        $event = m::mock(\Codeception\Event\SuiteEvent::class);
        $sut = new \Codeception\Extension\Cleansman([], [ 'silent' => false ]);

        // Actual cleanup happens here
        $sut->moduleInit($event);

        $this->assertFalse($this->vfsRoot->hasChild('file1'));
        $this->assertFalse($this->vfsRoot->hasChild('file2'));
        $this->assertFalse($this->vfsRoot->hasChild('dir1'));
    }
}
