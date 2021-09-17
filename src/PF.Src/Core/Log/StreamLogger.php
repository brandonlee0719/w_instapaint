<?php

namespace Core\Log;

class StreamLogger extends AbstractLogger
{
    /**
     * @var string
     */
    protected $filename;

    /**
     * constant 5 MB
     */
    protected $size_limit = 5242880;

    /**
     * StreamLogger constructor.
     *
     * @param $filename
     */
    public function __construct($filename)
    {
        $directory = PHPFOX_ROOT . 'PF.Base' . PHPFOX_DS . 'file' . PHPFOX_DS
            . 'log';

        if (!is_dir($directory)) {
            @mkdir($directory, 0777, true);
        }

        $this->filename = $directory . PHPFOX_DS . $filename;

        if (file_exists($this->filename)
            and filesize($this->filename) > $this->size_limit
            and rename($this->filename, $this->filename . '.' . time())
        ) {
            rename($this->filename, $this->filename . '.' . time());
        }

        if (!file_exists($this->filename)) {
            file_put_contents($this->filename, '# stream logger',
                FILE_APPEND);
            chmod($this->filename, 0777);
        }
    }

    public function log($level, $message, $context = [])
    {
        if (!is_scalar($message) and !@is_string($message)) {
            $message = var_export($message, true);
        }
        $content = implode(PHP_EOL, [
            $level,
            date('Y-m-d H:i:s'),
            empty($context) ? $message
                : $this->interpolate($message, $context),
            PHP_EOL,
        ]);

        if (null != ($fp = fopen($this->filename, 'a+'))) {
            fwrite($fp, $content);
            fclose($fp);
        }
    }
}