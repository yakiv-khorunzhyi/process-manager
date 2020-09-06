<?php

declare(strict_types=1);

namespace Core\Processes;

class Serializator
{
    /**
     * @param \Core\Executable|\Closure $obj
     *
     * @return string
     */
    public static function serialize($obj): string
    {
        if ($obj instanceof Closure) {
            $obj = new SerializableClosure($obj);
        }

        return base64_encode(serialize($obj));
    }

    /**
     * @param string $serializedObj
     *
     * @return \Core\Executable|\Closure
     */
    public static function unserialize(string $serializedObj)
    {
        return unserialize(base64_decode($serializedObj));
    }
}