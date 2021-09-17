<?php

namespace Core\View;

class Environment extends \Twig_Environment
{
    public function render($name, array $params = [])
    {

        $params['ActiveUser'] = (new \Api\User())->get(\Phpfox::getService('user.auth')->getUserSession());
        $params['isPager'] = (isset($_GET['page']) ? true : false);
        $params['Is'] = new \Core\Is();

        return $this->loadTemplate($name)->render($params);
    }
}