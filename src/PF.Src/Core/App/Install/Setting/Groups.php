<?php

namespace Core\App\Install\Setting;

/**
 * Class Groups
 *
 * @author  Neil
 * @version 4.5.0
 * @package Core\App\Install\Setting
 */
class Groups
{
    const TYPE_RADIO    = "input:radio";
    const TYPE_TEXT     = "input:text";
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
    private $_option;

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
     * @return mixed
     */
    public function getOption()
    {
        return $this->_option;
    }

    /**
     * @param mixed $option
     */
    public function setOption($option)
    {
        $this->_option = $option;
    }

    public function isValid()
    {
        return true;
    }

    /**
     * get a phrase var_name generate for this user group setting
     *
     * @return string
     */
    public function getPhraseVarName()
    {
        return "user_group_setting_phrase_" . $this->_var_name;
    }

    /**
     * Get a phrase value of this user group setting (mean: info)
     *
     * @return string
     */
    public function getPhraseValue()
    {
        return $this->_info;
    }

    /**
     * Return error message if there is an error when init user group setting
     *
     * @return bool|string
     */
    public function getError()
    {
        return false;
    }

}