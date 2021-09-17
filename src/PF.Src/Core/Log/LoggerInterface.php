<?php

namespace Core\Log;

interface LoggerInterface
{
    /**
     * System is unusable.
     *
     * @param mixed $message
     * @param array $context
     */
    public function emergency($message, $context = []);

    /**
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     *
     * @param mixed $message
     * @param array $context
     */
    public function alert($message, $context = []);

    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @param mixed $message
     * @param array $context
     */
    public function critical($message, $context = []);

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param mixed $message
     * @param array $context
     */
    public function error($message, $context = []);

    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @param mixed $message
     * @param array $context
     */
    public function warning($message, $context = []);

    /**
     * Normal but significant events.
     *
     * @param mixed $message
     * @param array $context
     */
    public function notice($message, $context = []);

    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     *
     * @param mixed $message
     * @param array $context
     */
    public function info($message, $context = []);

    /**
     * Detailed debug information.
     *
     * @param mixed $message
     * @param array $context
     */
    public function debug($message, $context = []);

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param mixed $message
     * @param array $context
     */
    public function log($level, $message, $context = []);
}