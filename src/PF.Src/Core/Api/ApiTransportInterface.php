<?php

namespace Core\Api;

interface ApiTransportInterface
{

    /**
     * @return bool
     * @throws \Exception
     */
    function isUser();

    /**
     * @return boolean
     */
    function authorization();


    /**
     * @param $params
     *
     * @return mixed
     */
    function initSearchParams($params = []);

    /**
     * @description: require params for the request
     *
     * @param array $data
     * @param array $requires
     *
     * @return bool
     * @throws \Exception
     */
    public function requireParams($data, $requires = null);

    /**
     * @param $content
     *
     * @return mixed
     */
    function processContent($content);

    /**
     * @param $status
     * @param $data
     * @param $messages
     *
     * @return array
     */
    function processReturn($status, $data, $messages = null);

    /**
     * @description: return success status, data and messages
     *
     * @param array $data
     * @param array $messages
     *
     * @return array|bool
     */
    public function success($data = [], $messages = []);

    /**
     * @description: handle error for the request
     *
     * @param string $error
     * @param  bool  $ignoredLast
     *
     * @return array|bool
     */
    public function error($error = null, $ignoredLast = false);

}