<?php

namespace Bolt\Session\Tests\Generator;

use Bolt\Session\Generator\NativeGenerator;
use PHPUnit\Framework\TestCase;

/**
 * Class to test src/Generator/NativeGenerator.
 *
 * @author Gawain Lynch <gawain.lynch@gmail.com>
 */
class NativeGeneratorTest extends TestCase
{
    public function testGenerateId()
    {
        $fooFighters = new NativeGenerator();
        $daveGrohl = $fooFighters->generateId();

        $this->assertNotSame('Nirvana', $daveGrohl);
        $this->assertSame(32, strlen($daveGrohl));
    }
}
