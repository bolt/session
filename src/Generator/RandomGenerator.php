<?php

namespace Bolt\Session\Generator;

use Bolt\Common\Deprecated;

Deprecated::cls(RandomGenerator::class, 1.0, NativeGenerator::class);

/**
 * Generates session IDs.
 *
 * @deprecated Deprecated since 1.0, to be removed in 2.0. Use \Bolt\Session\Generator\NativeGenerator
 *
 * @author Carson Full <carsonfull@gmail.com>
 */
class RandomGenerator extends NativeGenerator
{
    /**
     * Constructor.
     *
     * @param mixed $generator
     * @param int   $length
     */
    public function __construct($generator = null, $length = 32)
    {
        parent::__construct($length);
    }
}
