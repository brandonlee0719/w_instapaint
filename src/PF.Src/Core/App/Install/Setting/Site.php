<?php

namespace Core\App\Install\Setting;

/**
 * Class Site
 *
 * @author  Neil
 * @version 4.5.0
 * @package Core\App\Install\Setting
 */
class Site
{
    const TYPE_RADIO    = "input:radio";
    const TYPE_TEXT     = "input:text";
    const TYPE_SELECT   = "select";
    const TYPE_PASSWORD = "password";

//    const TYPE_BOOLEAN = 'boolean';
//    const TYPE_STRING = 'string';
//    const TYPE_INTEGER = 'integer';
//    const TYPE_LARGE_STRING = 'large_string';
//    const TYPE_DROP = 'drop';
//    const TYPE_ARRAY = 'array';
    const TYPE_CURRENCY = 'currency';

    /** @const */
    public static $OPTION_YES_NO
        = [
            "yes" => "Yes",
            "no"  => "No",
        ];

    private $_var_name;
    private $_info;
    private $_type;
    private $_value;

    public function __construct($aParam = [])
    {
        if (isset($aParam['var_name'])) {
            $this->_var_name = $aParam['var_name'];
        }

        if (isset($aParam['info'])) {
            $this->_info = $aParam['info'];
        }

        if (isset($aParam['type'])) {
            $this->_type = $aParam['type'];
        }

        if (isset($aParam['value'])) {
            $this->_value = $aParam['value'];
        }
    }

    /**
     * @return mixed
     */
    public function getVarName()
    {
        return $this->_var_name;
    }

    /**
     * @param mixed $var_name
     */
    public function setVarName($var_name)
    {
        $this->_var_name = $var_name;
    }

    /**
     * @return mixed
     */
    public function getInfo()
    {
        return $this->_info;
    }

    /**
     * @param mixed $info
     */
    public function setInfo($info)
    {
        $this->_info = $info;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->_type = $type;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->_value;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value)
    {
        $this->_value = $value;
    }

    /**
     * get a phrase var_name generate for this setting
     *
     * @return string
     */
    public function getPhraseVarName()
    {
        return "setting_phrase_" . $this->_var_name;
    }

    /**
     * Get a phrase value of this setting (mean: info)
     *
     * @return string
     */
    public function getPhraseValue()
    {
        return $this->_info;
    }

    public function isValid()
    {
        return true;
    }

    /**
     * Return error message if there is an error when init setting
     *
     * @return bool|string
     */
    public function getError()
    {
        return false;
    }

}