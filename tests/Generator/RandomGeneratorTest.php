<?php

namespace Bolt\Session\Tests\Generator;

use Bolt\Session\Generator\RandomGenerator;
use PHPUnit\Framework\TestCase;

/**
 * Class to test src/Generator/RandomGenerator.
 *
 * @group legacy
 *
 * @author Gawain Lynch <gawain.lynch@gmail.com>
 */
class RandomGeneratorTest extends TestCase
{
    public function testGenerateId()
    {
        $fooFighters = new RandomGenerator(null, 42);
        $daveGrohl = $fooFighters->generateId();

        $this->assertNotSame('Nirvana', $daveGrohl);
        $this->assertSame(42, strlen($daveGrohl));
    }
}
