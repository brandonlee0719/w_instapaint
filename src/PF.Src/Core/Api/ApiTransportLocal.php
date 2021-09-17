<?php
/**
 * Created by PhpStorm.
 * User: namnv
 * Date: 10/14/16
 * Time: 9:48 AM
 */

namespace Core\Api;


use Phpfox;
use Phpfox_Error;

/**
 * Class ApiTransportLocal
 *
 * @package Core\Api
 */
class ApiTransportLocal extends \Phpfox_Service implements ApiTransportInterface
{
    /**
     * @inheritdoc
     */
    function isUser()
    {
        return Phpfox::isUser();
    }

    /**
     * @inheritdoc
     */
    function authorization()
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function requireParams($data, $requires = null)
    {
        foreach ($data as $param) {
            if ($requires === null) {
                $value = \Phpfox_Request::instance()->get($param, null);
                if (empty($value) && $value != '0') {
                    Phpfox_Error::set(_p('Param "{{ field }}" is required.', ['field' => $param]));
                }
            } else {
                if (is_array($requires) && (!isset($requires[$param]) || $requires[$param] == '')) {
                    Phpfox_Error::set(_p('Field "{{ field }}" is required.', ['field' => $param]));
                }
            }
        }
        if (Phpfox_Error::isPassed()) {
            return true;
        }
        throw new \Exception('');
    }

    /**
     * @inheritdoc
     */
    function initSearchParams($params = [])
    {
        foreach ($params as $key => $value) {
            if ($newValue = $this->request()->get($key, null)) {
                $params[$key] = $newValue;
            }
        }

        return $params;
    }

    /**
     * @inheritdoc
     */
    function processContent($content)
    {
        if (!Phpfox_Error::isPassed()) {
            return $this->error();
        }
        if (is_string($content)) {
            return $this->processReturn('success', [], [$content]);
        }

        if (!is_array($content)) {
            $content = (array)$content;
        }

        return $content;
    }

    /**
     * @inheritdoc
     */
    function processReturn($status, $data, $messages = null)
    {
        return ['status' => $status, 'data' => $data, 'messages' => $messages];
    }

    /**
     * @inheritdoc
     */
    function error($error = null, $ignoredLast = false)
    {
        if ($error !== null) {
            if (!$ignoredLast || Phpfox_Error::isPassed()) {
                Phpfox_Error::set($error);
            }
        }
        if (Phpfox_Error::isPassed()) {
            return $this->success();
        }
        return $this->processReturn('failed', [], Phpfox_Error::get());
    }

    /**
     * @inheritdoc
     */
    function success($data = [], $messages = [])
    {
        if (!Phpfox_Error::isPassed()) {
            return $this->error();
        }
        return $this->processReturn('success', $data, $messages);
    }
}