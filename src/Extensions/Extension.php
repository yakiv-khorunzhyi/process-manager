<?php

namespace Core\Extensions;

class Extension
{
    /**
     * @param array $names
     *
     * @return bool
     */
    public static function has(array $names): bool
    {
        foreach ($names as &$name) {
            if (!extension_loaded($name)) {
                return false;
            }
        }

        return true;
    }
}