<?php

namespace Core\Log;

class LogManager
{
    protected $loggers = [];

    /**
     * @param string $key
     *
     * @return LoggerInterface
     */
    public function get($key)
    {
        return isset($this->loggers[$key])
            ? $this->loggers[$key]
            : $this->loggers [$key] = $this->make($key);
    }

    /**
     * @param string $key
     *
     * @return LoggerInterface
     */
    public function make($key)
    {
        return new StreamLogger($key);
    }
}