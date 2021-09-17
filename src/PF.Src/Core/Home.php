<?php

namespace Core;

/**
 * Class Home
 *
 * @package Core
 *
 * @method Home token($token)
 * @method Home verify(array $param)
 * @method Home install()
 * @method Home vendor($name)
 * @method Home admincp($args)
 * @method Home products($args)
 * @method Home trial($param)
 * @method Home downloads(array $type)
 * @method Home key()
 * @method Home sync()
 * @method Home user()
 * @method Home status()
 * @method Home im()
 * @method Home im_token()
 * @method Home info()
 * @method Home uninstall($product_id)
 * @method Home install_token($token_id)
 * @params  String token
 */
class Home
{
    private $_id;
    private $_key;


    public function __construct($id = null, $key = null)
    {
        $this->_id = $id;
        $this->_key = $key;
    }

    public function checkAppInstalled($params)
    {
        $App = new \Core\App();
        try {
            $_App = $App->get($params['id']);

            return [
                'installed' => 'yes',
                'version'   => $_App->vendor,
            ];
        } catch (\Exception $e) {
            return [
                'error'   => true,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function run($action, $response = null)
    {
        header('Content-type: application/json');
        echo(new Home\Run($action, $response));
        exit;
    }

    public static function store()
    {
        return (defined('PHPFOX_STORE_URL') ? PHPFOX_STORE_URL : 'https://store.phpfox.com/');
    }

    public static function url()
    {
        return (defined('PHPFOX_API_URL') ? PHPFOX_API_URL : 'https://api.phpfox.com/');
    }

    public function __call($method, $args)
    {
        $url = self::url() . $method;
//		$url = self::url() . 'install';
        $Http = new HTTP($url);
        $Http->auth($this->_id, $this->_key);
        if (\Phpfox::isTrial()) {
            $Http->header('PHPFOX_IS_TRIAL', '1');
        }

        $Http->using(['domain' => \Phpfox::getParam('core.path')]);
        $Http->using(['version' => \Phpfox::VERSION]);
        $Http->using(['version_build' => \Phpfox::PRODUCT_BUILD]);

        foreach ($args as $key => $value) {
            if (is_string($value)) {
                // $value = [$key => $value];
            }
            $Http->using($value);
        }
        return $Http->post();
    }
}