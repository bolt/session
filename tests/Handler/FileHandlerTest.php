<?php

namespace Bolt\Session\Tests\Handler;

use Bolt\Session\Handler\FileHandler;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\TestCase;

/**
 * Class to test src/Handler/FileHandler.
 *
 * @author Gawain Lynch <gawain.lynch@gmail.com>
 */
class FileHandlerTest extends TestCase
{
    /** @var vfsStreamDirectory */
    protected $vfs;
    /** @var string */
    protected $savePath;
    /** @var string */
    protected $sessionName;
    /** @var string */
    protected $sessionFile;

    public function setUp()
    {
        $this->vfs = VfsStream::setup();
        $this->savePath = $this->vfs->url();
        $this->sessionName = 'george';
        $this->sessionFile = $this->savePath . '/' . $this->sessionName . '.sess';
    }

    public function testConstructor()
    {
        $this->assertClassHasAttribute('savePath', FileHandler::class);
        $this->assertClassHasAttribute('fs', FileHandler::class);

        $fsh = new FileHandler($this->savePath);

        $this->assertObjectHasAttribute('savePath', $fsh);
        $this->assertObjectHasAttribute('fs', $fsh);

        $this->assertAttributeEquals($this->savePath, 'savePath', $fsh);
    }

    public function testOpen()
    {
        $fsh = new FileHandler($this->savePath);

        $result = $fsh->open($this->savePath, $this->sessionName);
        $this->assertTrue($result);
    }

    public function testClose()
    {
        $fsh = new FileHandler($this->savePath);

        $result = $fsh->close();
        $this->assertTrue($result);
    }

    /**
     * @covers \Bolt\Session\Handler\FileHandler::write
     * @covers \Bolt\Session\Handler\FileHandler::read
     */
    public function testWriteRead()
    {
        $fsh = new FileHandler($this->savePath);
        $fsh->open($this->savePath, $this->sessionName);

        $result = $fsh->write($this->sessionName, 'kittens');
        $this->assertTrue($result);

        $result = $fsh->read($this->sessionName);
        $this->assertSame('kittens', $result);
        $this->assertStringEqualsFile($this->sessionFile, 'kittens');
    }

    public function testDestroy()
    {
        $fsh = new FileHandler($this->savePath);
        $fsh->open($this->savePath, $this->sessionName);

        $fsh->write($this->sessionName, 'kittens');
        $this->assertFileExists($this->sessionFile);

        $result = $fsh->destroy($this->sessionName);
        $this->assertTrue($result);
        $this->assertFileNotExists($this->sessionName);
    }

    public function testGc()
    {
        $fsh = new FileHandler($this->savePath);
        $fsh->open($this->savePath, $this->sessionName);

        $fsh->write($this->sessionName, 'kittens');
        $this->assertFileExists($this->sessionFile);

        sleep(1);
        $result = $fsh->gc(1);
        $this->assertTrue($result);
        $this->assertFileNotExists($this->sessionName);
    }
}
