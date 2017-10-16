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
    protected $sessionId;
    /** @var string */
    protected $sessionFile;

    public function setUp()
    {
        $this->vfs = VfsStream::setup();
        $this->savePath = $this->vfs->url();
        $this->sessionId = 'george';
        $this->sessionFile = $this->savePath . '/' . $this->sessionId . '.sess';
    }

    public function testOpen()
    {
        $fsh = new FileHandler($this->savePath);

        $result = $fsh->open(null, 'PHPSESSID');
        $this->assertTrue($result);
    }

    public function testClose()
    {
        $fsh = new FileHandler($this->savePath);

        $result = $fsh->close();
        $this->assertTrue($result);
    }

    public function testRead()
    {
        file_put_contents($this->sessionFile, 'kittens');

        $fsh = new FileHandler($this->savePath);

        $result = $fsh->read($this->sessionId);
        $this->assertSame('kittens', $result);
    }

    public function testReadNew()
    {
        $fsh = new FileHandler($this->savePath);

        $result = $fsh->read('new');
        $this->assertSame('', $result);
    }

    public function testReadUnreadable()
    {
        file_put_contents($this->savePath . '/unreadable.sess', '');
        chmod($this->savePath . '/unreadable.sess', 0);

        $fsh = new FileHandler($this->savePath);

        $result = $fsh->read('unreadable');
        $this->assertSame('', $result);
    }

    public function testWrite()
    {
        $fsh = new FileHandler($this->savePath);

        $result = $fsh->write($this->sessionId, 'kittens');
        $this->assertTrue($result);
        $this->assertFileExists($this->sessionFile);
        $this->assertStringEqualsFile($this->sessionFile, 'kittens');
    }

    public function testDestroy()
    {
        file_put_contents($this->sessionFile, 'kittens');

        $fsh = new FileHandler($this->savePath);

        $result = $fsh->destroy($this->sessionId);
        $this->assertTrue($result);
        $this->assertFileNotExists($this->sessionFile);
    }

    public function testGc()
    {
        file_put_contents($this->sessionFile, 'kittens');
        touch($this->sessionFile, time() - 5);

        $fsh = new FileHandler($this->savePath);

        $result = $fsh->gc(1);
        $this->assertTrue($result);
        $this->assertFileNotExists($this->sessionFile);
    }
}
