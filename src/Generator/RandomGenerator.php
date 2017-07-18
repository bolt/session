<?php

namespace Bolt\Session\Generator;

use Bolt\Common\Deprecated;

Deprecated::cls(RandomGenerator::class, 3.3, NativeGenerator::class);

/**
 * Generates session IDs.
 *
 * @deprecated Deprecated since 3.3, to be removed in 4.0. Use \Bolt\Session\Generator\NativeGenerator
 *
 * @author Carson Full <carsonfull@gmail.com>
 */
class RandomGenerator extends NativeGenerator
{
    /**
     * Constructor.
     *
     * @param mixed   $generator
     * @param integer $length
     */
    public function __construct($generator = null, $length = 32)
    {
        parent::__construct($length);
    }
}
