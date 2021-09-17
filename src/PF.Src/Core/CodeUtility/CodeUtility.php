<?php

namespace Core\CodeUtility;

use Phpfox;

class CodeUtility
{
    /**
     * @return array
     */
    public function getModuleIdList()
    {
        return array_map(function ($v) {
            return $v['module_id'];
        }, Phpfox::getLib('database')
            ->select('module_id')
            ->from(':setting')
            ->group('module_id')
            ->execute('getSlaveRows'));
    }

    /**
     * @param $phrase
     *
     * @return array
     */
    public function parseSettingPhrase($phrase)
    {
        $values = explode('</title><info>', _p($phrase, 'en'), 2);
        $info = $values[0];
        $info = strip_tags($info);
        $note = isset($values[1]) ? $values[1] : '';
        $note = strip_tags($note, '<br/><a><i><b><strong>');

        $info = str_replace(['\n', '\r'], [' ', ' '], $info);
        $note = str_replace([PHP_EOL, '\r', '\n'], [' ', ' ', ' '], nl2br($note));

        return [$info, $note];
    }

    /**
     * @param string $typeId
     * @param string $valueDefault
     *
     * @return array
     */
    public function parseSettingValue($typeId, $valueDefault)
    {
        switch ($typeId) {
            case 'drop':
                $aArray = unserialize($valueDefault);
                return [$aArray['values']['values'], $aArray['values']];
            case 'drop_with_key':
            case 'select':
            case 'input:radio':
            case 'radio':
                $aArray = unserialize($valueDefault);
                return [$aArray['values']['default'], $aArray['values']];
            case 'multi_text':
            case 'currency':
                $aArray = [];
                $valueDefault = preg_replace_callback("/s:([0-9]+):\"(.*?)\";/is", function ($matches) {
                    return "s:" . strlen($matches[2]) . ":\"{$matches[2]}\";";
                }, $valueDefault);

                if (is_array(unserialize($valueDefault))) {
                    $aArray = unserialize($valueDefault);
                } else {
                    eval("\$aArray = " . unserialize($valueDefault) . "");
                }
                return [$aArray, null];
            case 'array':
                $aArray = [];
                $valueDefault = preg_replace_callback("/s:([0-9]+):\"(.*?)\";/is", function ($matches) {
                    return "s:" . strlen($matches[2]) . ":\"{$matches[2]}\";";
                }, $valueDefault);
                if (is_array(unserialize($valueDefault))) {
                    $aArray = unserialize($valueDefault);
                } else {
                    @eval("\$aArray = " . unserialize($valueDefault) . "");
                }
                return [$aArray, null];
            default:
        }
        return [$valueDefault, null];
    }

    /**
     * @param string $moduleId
     *
     * @return array
     */
    public function getGroupStructure($moduleId)
    {
        $rows = Phpfox::getLib('database')
            ->select('*')
            ->from(':setting_group')
            ->where(['module_id' => $moduleId])
            ->execute('getRows');

        $result = [];

        foreach ($rows as $row) {
            list($info, $note) = $this->parseSettingPhrase($row['var_name']);
            $result[] = [
                'group_id'    => $row['group_id'],
                'version_id'  => $row['version_id'],
                'module_id'   => $row['module_id'],
                'info'        => $info,
                'description' => $note,
            ];
        }
        return $result;

    }

    public function getSettingStructure($moduleId)
    {
        $rows = Phpfox::getLib('database')
            ->select('*')
            ->from(':setting')
            ->where(['module_id' => $moduleId])
            ->order('ordering')
            ->execute('getRows');

        $result = [];

        $ordering = 0;
        foreach ($rows as $row) {
            list($info, $note) = $this->parseSettingPhrase($row['phrase_var_name']);
            list($value, $options) = $this->parseSettingValue($row['type_id'], $row['value_default']);
            $item = [
                'var_name'    => $row['var_name'],
                'type'        => $row['type_id'],
                'info'        => $info,
                'description' => $note,
                'is_hidden'   => $row['is_hidden'],
                'value'       => $value,
                'options'     => $options,
                'group_id'    => $row['group_id'],
                'ordering'    => $ordering++,
                'version_id'  => $row['version_id'],
                'module_id'   => $row['module_id'],
            ];

            foreach ($item as $key => $value) {
                if ($value === null) {
                    unset($item[$key]);
                }
            }

            $result[] = $item;
        }
        return $result;
    }

    /**
     * @param $moduleId
     *
     * @return array
     */
    public function getStructure($moduleId)
    {
        $rows = Phpfox::getLib('database')
            ->select('module_id')
            ->from(':setting')
            ->where(['module_id' => $moduleId])
            ->group('module_id')
            ->order('ordering')
            ->execute('getSlaveRows');
        // convert old setting to new settings

    }
}