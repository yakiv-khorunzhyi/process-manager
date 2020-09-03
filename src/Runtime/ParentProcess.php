<?php

declare(strict_types=1);

namespace Core\Runtime;

use Core\Exceptions\FileNotFoundException;
use Core\Executable;
use Core\Processes\SimpleProcess;

class ParentProcess
{
    /** @var string */
    protected $autoloader;

    /** @var int */
    protected $id = 0;

    /** @var int */
    protected $pid = 0;

    /** @var string */
    protected $childProcessScriptPath = __DIR__ . '/ChildRuntime.php';

    public function __construct(string $autoloaderPath)
    {
        if (!file_exists($autoloaderPath)) {
            throw new FileNotFoundException(
                "Autoloader file not found by path: {$autoloaderPath}."
            );
        }

        $this->autoloader = $autoloaderPath;
    }

    public function create($task, string $pathToPhp): Runnable
    {
        $process = new SimpleProcess([
            $pathToPhp,
            $this->childProcessScriptPath,
            $this->autoloader,
            $this->encode($task),
        ]);

        return
        return ParallelProcess::create($process, self::getId());
    }

    /**
     * @param Executable|\Closure $task
     *
     * @return string
     */
    public function encode($task): string
    {
        if ($task instanceof Closure) {
            $task = new SerializableClosure($task);
        }

        return base64_encode(serialize($task));
    }
}