<?php

namespace Bolt\Session\Tests\Serializer;

use Bolt\Session\Serializer\NativeSerializer;
use PHPUnit\Framework\TestCase;

/**
 * Class to test src/Serializer/NativeSerializer.
 *
 * @author Gawain Lynch <gawain.lynch@gmail.com>
 */
class NativeSerializerTest extends TestCase
{
    public function testSerialize()
    {
        $cornFlakes = new NativeSerializer();
        $captCrunch = $cornFlakes->serialize(['milk' => 'bowl']);

        $this->assertSame($captCrunch, 'a:1:{s:4:"milk";s:4:"bowl";}');
    }

    public function testUnserialize()
    {
        $cornFlakes = new NativeSerializer();
        $weetBix = $cornFlakes->unserialize('a:1:{s:4:"milk";s:4:"bowl";}');

        $this->assertArrayHasKey('milk', $weetBix);
        $this->assertSame('bowl', $weetBix['milk']);
    }
}
