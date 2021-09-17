<?php

namespace Core\Log;

abstract class AbstractLogger implements LoggerInterface
{
    /**
     * Interpolates context values into the message placeholders.
     *
     * example context usages
     * <code>
     * interpolate('This bugs caused by {0} in file {1} line {2}',[
     *  'Media Service', 'media_service.class.php', 44
     * ])
     * </code>
     *
     * @param string $message
     * @param array  $context
     *
     * @return string
     */
    function interpolate($message, $context = [])
    {
        // build a replacement with braces around the context keys
        $replace = [];
        foreach ($context as $key => $val) {
            // check that the value can be casted to string
            if (!is_array($val)
                && (!is_object($val)
                    || method_exists($val, '__toString'))
            ) {
                $replace['{' . $key . '}'] = $val;
            }
        }
        return strtr($message, $replace);
    }


    public function emergency($message, $context = [])
    {
        $this->log(LogLevel::EMERGENCY, $message, $context);
    }

    public function alert($message, $context = [])
    {
        $this->log(LogLevel::ALERT, $message, $context);
    }

    public function critical($message, $context = [])
    {
        $this->log(LogLevel::CRITICAL, $message, $context);
    }

    public function error($message, $context = [])
    {
        $this->log(LogLevel::ERROR, $message, $context);
    }

    public function warning($message, $context = [])
    {
        $this->log(LogLevel::WARNING, $message, $context);
    }

    public function notice($message, $context = [])
    {
        $this->log(LogLevel::NOTICE, $message, $context);
    }

    public function info($message, $context = [])
    {
        $this->log(LogLevel::INFO, $message, $context);
    }

    public function debug($message, $context = [])
    {
        $this->log(LogLevel::DEBUG, $message, $context);
    }
}