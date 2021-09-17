<?php

namespace Core\Request;

/**
 * Interface RequestInterface
 * <code>
 * $obj = new Request();
 *
 * $obj->init($url, $options);
 * $obj->format('json');
 * $obj->get()
 * // return json object
 * $ojb->post()
 * // return array
 * </code>
 *
 * @package Core\Request
 */
interface RequestInterface
{

    /**
     * @param string $format available options: json, xml, null
     *
     * @return mixed
     */
    public function setFormat($format);


    /**
     * @param array $options
     *
     * @return mixed
     */
    public function setOptions($options = []);

    /**
     *
     * execute HTTP GET request
     *
     * @return mixed
     * @throws Exception
     */
    public function get();

    /**
     * Execute Post method
     *
     * @return mixed
     * @throws Exception
     *
     */
    public function post();

    /**
     * @param $destination
     *
     * @return true
     * @throws Exception
     */
    public function download($destination);
}