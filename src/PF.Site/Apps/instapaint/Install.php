<?php

namespace Apps\Instapaint;

use Core\App;

/**
 * Class Install
 * @author  Neil J. <neil@phpfox.com>
 * @version 4.6.0
 * @package Apps\Instapaint
 */
class Install extends App\App
{

    private $_app_phrases = [

    ];

    protected function setId()
    {
        $this->id = 'Instapaint';
    }

    protected function setAlias()
    {
        $this->alias = 'instapaint'; // i.e. module
    }

    protected function setName()
    {
        $this->name = 'Instapaint';
    }

    protected function setVersion()
    {
        $this->version = '4.6.0';
    }

    protected function setSupportVersion()
    {
        $this->start_support_version = '4.6.0';
        $this->end_support_version = '4.6.0';
    }

    protected function setSettings()
    {
    }

    protected function setUserGroupSettings()
    {
    }

    protected function setComponent()
    {
    }

    protected function setComponentBlock()
    {
    }

    protected function setPhrase()
    {
        $this->phrase = $this->_app_phrases;
    }

    protected function setOthers()
    {

        $this->_apps_dir = 'instapaint';
        $this->_publisher = 'Ivan';
    }
}
