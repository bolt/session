<?php

namespace Bolt\Session\Serializer;

use Bolt\Common\Serialization;

class NativeSerializer implements SerializerInterface
{
    /**
     * {@inheritdoc}
     */
    public function serialize($data)
    {
        return Serialization::dump($data);
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($data)
    {
        return Serialization::parse($data);
    }
}
