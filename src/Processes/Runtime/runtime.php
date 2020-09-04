<?php

//todo: Yakiv - add path to ProcessManager('runtime.php'), for add self runtime scripts
try {
    $autoloader     = $argv[1];
    $serializedTask = $argv[2] ?? null;

    require_once($autoloader);

    if (!$serializedTask) {
        throw new InvalidArgumentException('Invalid closure.');
    }

    $task = \Core\Processes\TaskPacker::unpack($serializedTask);
    $output = $task();

    fwrite(STDOUT, base64_encode(serialize($output)));

    exit(0);
} catch (Throwable $t) {
    fwrite(STDERR, base64_encode(serialize($output)));

    exit(1);
}