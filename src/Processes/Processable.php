<?php

namespace Core\Processes;

interface Processable
{
    public function getId(): int;

    public function getPid(): ?int;

    public function start();

    /**
     * @param callable $callback
     *
     * @return static
     */
    public function then(callable $callback);

    /**
     * @param callable $callback
     *
     * @return static
     */
    public function catch(callable $callback);
}