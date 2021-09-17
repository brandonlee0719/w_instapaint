<?php

namespace Core\Request;


class CurlRequest implements RequestInterface
{
    /**
     * @var \resource
     */
    private $ch;

    /**
     * @var string
     */
    private $format;

    /**
     * @var string
     */
    private $error;

    /**
     * @var int
     */
    private $error_no;

    private $url;

    public function setFormat($format)
    {
        $this->format = $format;
    }

    function __construct($options = [])
    {
        $this->ch = curl_init();

        // default options
        curl_setopt_array($this->ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_FOLLOWLOCATION => true,
        ]);

        if (!empty($options)) {
            $this->setOptions($options);
        }
    }

    public function setOptions($options = [])
    {
        foreach ($options as $k => $v) {
            if (method_exists($this, $method = 'set' . ucfirst($k))) {
                $this->{$method}($v);
            }
        }
    }

    public function setUrl($value)
    {
        $this->url = $value;
        curl_setopt($this->ch, CURLOPT_URL, $value);

    }

    public function setTimeout($value)
    {
        curl_setopt($this->ch, CURLOPT_TIMEOUT, intval($value));
    }

    public function get()
    {
        // TODO: Implement get() method.
    }

    public function post()
    {
        // TODO: Implement post() method.
    }

    public function download($destination)
    {
        if (file_exists($destination) and !@unlink($destination)) {
            throw new Exception(_p('oops_file_destination_exists_but_is_un_writable', ['destination' => $destination]));
        }

        $dir = dirname($destination);

        if (!@is_dir($dir)) {
            if (!@mkdir($dir, 0777, true)) {
                throw new Exception(_p('oops_could_not_make_dir', ['dir' => $dir]));
            }
            @chmod($dir, 0777);
        }

        $content = curl_exec($this->ch);

        $this->error = curl_error($this->ch);

        $this->error_no = curl_errno($this->ch);

        if ($this->error_no) {
            throw new Exception(_p('Oops! Could not download ') . $this->url . ' '
                . $this->error);
        }

        $result = @file_put_contents($destination, $content);

        if (!$result) {
            throw new Exception(_p('oops_could_not_write_content_to_destination', ['destination' => $destination]));
        }

        unset($content);

        return true;
    }

    public function getError()
    {
        return $this->error;
    }

    /**
     * @codeCoverageIgnore
     */
    public function __destruct()
    {
        if ($this->ch) {
            @curl_close($this->ch);
        }
    }
}