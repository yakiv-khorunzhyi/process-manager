<?php

declare(strict_types=1);

namespace Core\Processes;

class TaskPacker
{
    /**
     * @param \Core\Executable|\Closure $task
     *
     * @return string
     */
    public static function pack($task): string
    {
        if ($task instanceof Closure) {
            $task = new SerializableClosure($task);
        }

        return base64_encode(serialize($task));
    }

    /**
     * @param string $serializedTask
     *
     * @return \Core\Executable|\Closure
     */
    public static function unpack(string $serializedTask)
    {
        return unserialize(base64_decode($serializedTask));
    }
}