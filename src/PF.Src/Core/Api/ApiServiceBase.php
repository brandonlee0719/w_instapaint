<?php

namespace Core\Api;

use Phpfox;
use Phpfox_Error;

class ApiServiceBase extends \Phpfox_Service
{

    /***
     * @description: define which public fields of item will be returned
     * @var array
     */
    protected $_publicFields = [];

    /**
     * @description: define which general fields of item will be returned
     * @var array
     */
    protected $_generalFields = [];

    /**
     * @description: define which full fields of item will be returned
     * @var array
     */
    protected $_fullFields = [];

    /**
     * @description: define params for browsing items
     * @var array
     */
    protected $_searchParams = ['limit' => 10, 'page' => 0];

    /**
     * @var ApiTransportInterface
     */
    protected $_transport;

    /**
     * @return ApiTransportInterface;
     */
    public function getTransport()
    {
        return $this->_transport;
    }

    /**
     * @param ApiTransportInterface $transport
     */
    public function setTransport($transport)
    {
        $this->_transport = $transport;
    }

    /**
     * @description: get a search param by name
     *
     * @param      $name
     * @param null $default
     *
     * @return bool|mixed|null
     */
    public function getSearchParam($name, $default = null)
    {
        if (!isset($this->_searchParams[$name])) {
            if ($default === null) {
                Phpfox_Error::set(_p('Missing search param \'{{ name }}\'.', ['name' => $name]));
                return false;
            }
            return $default;
        }
        return $this->_searchParams[$name];
    }

    /**
     * @description: set public fields
     *
     * @param array $publicFields
     */
    public function setPublicFields($publicFields)
    {
        $this->_publicFields = $publicFields;
    }

    /**
     * @description: set general fields
     *
     * @param array $generalFields
     */
    public function setGeneralFields($generalFields)
    {
        $this->_generalFields = $generalFields;
    }

    /**
     * @description: set full fields
     *
     * @param array $fullFields
     */
    public function setFullFields($fullFields)
    {
        $this->_fullFields = $fullFields;
    }

    /**
     * @description: set search params
     *
     * @param      $searchParams
     * @param bool $merge
     */
    public function setSearchParams($searchParams, $merge = true)
    {
        if ($merge) {
            $this->_searchParams = array_merge($this->_searchParams, $searchParams);
        } else {
            $this->_searchParams = $searchParams;
        }
    }

    /**
     * @description: init search params
     */
    public function initSearchParams()
    {
        $aSearchParams = $this->getTransport()->initSearchParams($this->_searchParams);
        $this->setSearchParams($aSearchParams);
    }

    /**
     * @description: handle the job to return data of an item
     *
     * @param array  $aItem
     * @param string $sReturnMode
     * @param array  $fields
     *
     * @return array
     */
    public function getItem($aItem, $sReturnMode = 'public', $fields = [])
    {
        if (empty($fields)) {
            $fields = $this->_publicFields;
            switch ($sReturnMode) {
                case 'general':
                    $fields = array_merge($this->_publicFields, $this->_generalFields);
                    break;
                case 'full':
                    $fields = array_merge($this->_publicFields, $this->_generalFields, $this->_fullFields);
                    break;
                case 'all':
                    return $aItem;
            }
        }
        return array_intersect_key($aItem, array_flip($fields));
    }

    /**
     * @description: check is user
     * @return bool
     * @throws \Exception
     */
    public function isUser()
    {
        if (method_exists($this->getTransport(), 'isUser')) {
            return $this->getTransport()->isUser();
        }
        if (Phpfox::isUser()) {
            return true;
        }

        throw new \Exception(_p('This request requires an user token.'));
    }

    /**
     * Handle api request and return response
     *
     * @param array  $params
     * @param mixed  $transport
     * @param string $method
     *
     * @return array|bool
     *
     */
    public function process($params, $transport, $method)
    {
        $this->setTransport($transport);
        $transport->authorization();

        if (empty($params['maps'])) {
            $params['maps'] = [
                'get'    => 'get',
                'put'    => 'put',
                'post'   => 'post',
                'delete' => 'delete',
            ];
        }

        $method = isset($params['maps'][$method]) ? $params['maps'][$method] : $method;

        if (!method_exists($this, $method)) {
            return $this->error(_p('Method is\'t supported.'));
        }

        $content = $this->{$method}($params['args']);

        return $this->processContent($content);
    }

    /**
     * @description: handle error for the request
     *
     * @param string $error
     * @param  bool  $ignoredLast
     *
     * @return array|bool
     */
    public function error($error = null, $ignoredLast = false)
    {
        return $this->getTransport()->error($error, $ignoredLast);
    }

    /**
     * @description: return success status, data and messages
     *
     * @param array $data
     * @param array $messages
     *
     * @return array|bool
     */
    public function success($data = [], $messages = [])
    {
        return $this->getTransport()->success($data, $messages);
    }

    /**
     * @description: process return data
     *
     * @param string $status
     * @param array  $data
     * @param array  $messages
     *
     * @return array
     */
    public function processReturn($status = '', $data = [], $messages = [])
    {
        return $this->getTransport()->processReturn($status, $data, $messages);
    }

    /**
     * @description: process return content for the response
     *
     * @param $content
     *
     * @return array|bool
     */
    public function processContent($content)
    {
        return $this->getTransport()->processContent($content);
    }

    /**
     * @description: require params for the request
     *
     * @param array $aParams
     * @param array $requires
     *
     * @return bool
     * @throws \Exception
     */
    public function requireParams($aParams, $requires = null)
    {
        return $this->getTransport()->requireParams($aParams, $requires);
    }
}