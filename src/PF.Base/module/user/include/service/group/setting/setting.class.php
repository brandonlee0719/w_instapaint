<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class User_Service_Group_Setting_Setting
 */
class User_Service_Group_Setting_Setting extends Phpfox_Service
{
    /**
     * @var array
     */
    private $_aAlias = [];

    /**
     * @var array|mixed
     */
    private $_aParam = [];

    /**
     * @var int|string
     */
    private $_iLastUserGroupId = 0;

    /**
     * @var bool
     */
    private $_loadAlias = false;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('user_group_setting');

        $this->_aParam = $this->_setParam(Phpfox::getUserBy('user_group_id'));

        $this->_iLastUserGroupId = Phpfox::getUserBy('user_group_id');

    }

    /**
     * Get result for global search settings.
     *
     * @param array $aSkipModules
     *
     * @return array
     */
    public function getForSearch($aSkipModules = [])
    {
        $aRows = [];
        $oUrl = Phpfox::getLib('url');

        $phrases = [];
        $locale  = Phpfox::getLib('locale');
        $f  = function($p,$i) use (&$phrases,$locale){
            if (isset($phrases[$p])) return $phrases[$p];
            if ($i) return ($phrases[$p] = _p($p));
            if (Phpfox::isAppAlias($p)) {
                $sRealAppId  = Phpfox::getAppId($p);
                $App = \Core\Lib::appInit($sRealAppId);
            } elseif (Phpfox::isApps($p)) {
                $sRealAppId = $p;
                $App = \Core\Lib::appInit($sRealAppId);
            }
            return (empty($App) || empty($App->name)) ? $locale->translate($p, 'module') : $App->name;
        };

        $glue = ' <i>&raquo;<i> ';

        $aSettings = $this->database()
            ->select('user_group_setting.*, m.module_id AS module_name')
            ->from($this->_sTable, 'user_group_setting')
            ->join(Phpfox::getT('module'), 'm',
                'm.module_id = user_group_setting.module_id')
            ->execute('getSlaveRows');

        foreach ($aSettings as $aRow) {
            $aRows[] = [
                'module_id' => $aRow['module_id'],
                'title'     => strip_tags(htmlspecialchars_decode((Phpfox_Locale::instance()
                    ->isPhrase('admincp.user_setting_' . $aRow['name'])
                    ? _p('user_setting_' . $aRow['name']) : $aRow['name']))),
                'link'      => $oUrl->makeUrl('admincp.user.group.add', [
                    'group_id'      => 2,
                    'setting' => 1,
                    'module'  => $aRow['module_id'],
                ]) . '#iSettingId' . $aRow['setting_id'],
                'type'      => 'user_group_setting',
                'category'=> $f('members',1) . $glue. $f('user_group_settings',1) .$glue. $f($aRow['module_id'],0),
            ];
        }
        return $aRows;
    }

    public function getParam($sName, $default = null)
    {
        if (Phpfox::getUserBy('user_group_id') != $this->_iLastUserGroupId) {
            $this->_aParam
                = $this->_setParam(Phpfox::getUserBy('user_group_id'));
            $this->_iLastUserGroupId = Phpfox::getUserBy('user_group_id');
        }

        if (isset($this->_aAlias[$sName])) {
            $sName = $this->_aAlias[$sName];
        }

        if (isset($this->_aParam[$sName])) {
            $aUnserialize = @unserialize($this->_aParam[$sName]);
            return $aUnserialize ? $aUnserialize : $this->_aParam[$sName];
        }

        return $default;
    }

    public function getGroupParam($iGroupId, $sName, $default = null)
    {
        static $aGroup = [];

        if (!isset($aGroup[$iGroupId])) {
            $aGroup[$iGroupId] = $this->_setParam($iGroupId);
        }

        return isset($aGroup[$iGroupId][$sName]) ? $aGroup[$iGroupId][$sName]
            : $default;
    }

    /**
     * @param $iGroupId
     *
     * @return array|int|string
     * @since 4.6.0 does not support $iGroupId
     */
    public function getModules($iGroupId = null)
    {
        if ($iGroupId) {
            ;
        }

        $aModules = $this->database()
            ->select('m.module_id, COUNT(ugs.module_id) AS total_setting, m.phrase_var_name')
            ->from(Phpfox::getT('module'), 'm')
            ->join(Phpfox::getT('user_group_setting'), 'ugs',
                'ugs.module_id = m.module_id AND ugs.is_hidden = 0')
            ->where('m.is_active = 1')
            ->group('m.module_id')
            ->execute('getSlaveRows');

        foreach ($aModules as $index => $aModule) {
            if ($aModule['phrase_var_name'] == 'module_apps') {
                $aModules[$index]['title'] = _p('module_'
                    . $aModule['module_id']);
            } else {
                $aModules[$index]['title'] = Phpfox::getLib('locale')
                    ->translate($aModule['module_id'], 'module');
            }
        }

        return $aModules;
    }


    public function get($iGroupId, $iModuleId = null)
    {
        $excludes =  Phpfox::getExcludeSettingsConditions();

        switch ($iGroupId) {
            case ADMIN_USER_ID:
                $sVar = 'default_admin';
                break;
            case GUEST_USER_ID:
                $sVar = 'default_guest';
                break;
            case STAFF_USER_ID:
                $sVar = 'default_staff';
                break;
            case NORMAL_USER_ID:
                $sVar = 'default_user';
                break;
            default:
                break;
        }

        if (!isset($sVar)) {
            $sVar = 'default_value';

            $this->database()->select('ugc.default_value, inherit_id, ')
                ->leftJoin(Phpfox::getT('user_group_custom'), 'ugc',
                    'ugc.user_group_id = ' . (int)$iGroupId
                    . ' AND ugc.module_id = user_group_setting.module_id AND ugc.name = user_group_setting.name')
                ->join(Phpfox::getT('user_group'), 'ug',
                    'ug.user_group_id = ' . (int)$iGroupId);
        }

        $aRows = $this->database()
            ->select('user_group_setting.*, user_setting.value_actual, m.module_id AS module_name')
            ->from($this->_sTable, 'user_group_setting')
            ->leftJoin(Phpfox::getT('module'), 'm',
                'm.module_id = user_group_setting.module_id')
            ->leftJoin(Phpfox::getT('product'), 'product',
                'product.product_id = user_group_setting.product_id AND product.is_active = 1')
            ->leftJoin(Phpfox::getT('user_setting'), 'user_setting',
                "user_setting.user_group_id = '" . $iGroupId
                . "' AND user_setting.setting_id = user_group_setting.setting_id")
            ->order('m.module_id ASC, user_group_setting.ordering ASC')
            ->where('user_group_setting.is_hidden = 0' . ($iModuleId === null
                    ? ''
                    : ' AND user_group_setting.module_id = \'' . $iModuleId
                    . '\''))
            ->execute('getSlaveRows');

        $aSettings = [];
        foreach ($aRows as $aRow) {
            $moduleId  = $aRow['module_id'];
            if(isset($excludes[$moduleId]) and $excludes[$moduleId] == $aRow['product_id'])
                continue;

            $aParts = explode('</title><info>',
                \Core\Lib::phrase()->isPhrase('user_setting_' . $aRow['name'])
                    ? _p('user_setting_' . $aRow['name']) : $aRow['name']);

            $aRow['setting_name'] = strip_tags($aParts[0]);
            if (isset($aParts[1])) {
                $aRow['setting_info'] = strip_tags($aParts[1]);
            } else {
                $aRow['setting_info'] = '';
            }


            $aRow['setting_name'] = str_replace("\n", "<br />",
                $aRow['setting_name']);
            $aRow['user_group_id'] = $sVar;
            $aRow['values'] = unserialize($aRow['option_values']);
            $sModuleName = $aRow['module_name'];

            unset($aRow['module_name']);

            $this->_setType($aRow, $sVar);

            $aSettings[$aRow['product_id']][$sModuleName][] = $aRow;
        }

        return $aSettings;
    }

    public function getSetting($iId)
    {
        $aRow = $this->database()
            ->select('user_group_setting.*, m.module_id AS module_name')
            ->from($this->_sTable, 'user_group_setting')
            ->join(Phpfox::getT('module'), 'm',
                'm.module_id = user_group_setting.module_id')
            ->where('user_group_setting.setting_id = ' . (int)$iId)
            ->execute('getSlaveRow');

        $aRow['info'] = (Phpfox_Locale::instance()
            ->isPhrase('admincp.user_setting_' . $aRow['name'])
            ? _p('user_setting_' . $aRow['name']) : $aRow['name']);
        $aRow['module'] = $aRow['module_id'] . '|' . $aRow['module_name'];

        return $aRow;
    }

    public function export($sProductId, $sModuleId = null)
    {
        $aWhere = [];
        $aWhere[] = "user_group_setting.product_id = '" . $sProductId . "'";
        if ($sModuleId !== null) {
            $aWhere[] = "AND user_group_setting.module_id = '" . $sModuleId
                . "'";
        }

        $aRows = $this->database()
            ->select('user_group_setting.*, m.module_id AS module_name, product.title AS product_name')
            ->from($this->_sTable, 'user_group_setting')
            ->join(Phpfox::getT('module'), 'm',
                'm.module_id = user_group_setting.module_id')
            ->join(Phpfox::getT('product'), 'product',
                'product.product_id = user_group_setting.product_id')
            ->where($aWhere)
            ->execute('getSlaveRows');

        if (!count($aRows)) {
            return false;
        }

        $oXmlBuilder = Phpfox::getLib('xml.builder');
        $oXmlBuilder->addGroup('user_group_settings');
        $aCache = [];
        foreach ($aRows as $aRow) {
            if (isset($aCache[$aRow['name']])) {
                continue;
            }

            $aCache[$aRow['name']] = $aRow['name'];
            $oXmlBuilder->addTag('setting', $aRow['name'], [
                    'is_admin_setting' => $aRow['is_admin_setting'],
                    'module_id'        => $aRow['module_id'],
                    'type'             => $aRow['type_id'],
                    'admin'            => $aRow['default_admin'],
                    'user'             => $aRow['default_user'],
                    'guest'            => $aRow['default_guest'],
                    'staff'            => $aRow['default_staff'],
                    'module'           => $aRow['module_name'],
                    'ordering'         => $aRow['ordering'],
                ]
            );
        }
        $oXmlBuilder->closeGroup();

        return true;
    }

    /* This function gets the user groups that have enabled a setting.
     * Used to filter users in the user.browse service
     */
    public function getUserGroupsBySetting($sParam, $mValue = true)
    {
        // Get all user groups
        $aGroups = Phpfox::getService('user.group')->get();
        $aOut = [];
        foreach ($aGroups as $aGroup) {
            if ($this->getGroupParam($aGroup['user_group_id'], $sParam)
                == $mValue
            ) {
                $aOut[$aGroup['user_group_id']] = $aGroup;
            }
        }
        return $aOut;
    }

    /**
     * If a call is made to an unknown method attempt to connect
     * it to a specific plug-in with the same name thus allowing
     * plug-in developers the ability to extend classes.
     *
     * @param string $sMethod    is the name of the method
     * @param array  $aArguments is the array of arguments of being passed
     */
    public function __call($sMethod, $aArguments)
    {
        /**
         * Check if such a plug-in exists and if it does call it.
         */
        if ($sPlugin
            = Phpfox_Plugin::get('user.service_group_setting_setting__call')
        ) {
            return eval($sPlugin);
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::'
            . $sMethod . '()', E_USER_ERROR);
    }

    private function &_setType(&$aRow, $sVar)
    {

        if (empty($aRow['value_actual']) && $aRow['value_actual'] != '0') {
            if (is_null($aRow[$sVar]) && $aRow['inherit_id'] > 0) {
                switch ($aRow['inherit_id']) {
                    case ADMIN_USER_ID:
                        $sVar = 'default_admin';
                        break;
                    case GUEST_USER_ID:
                        $sVar = 'default_guest';
                        break;
                    case STAFF_USER_ID:
                        $sVar = 'default_staff';
                        break;
                    case NORMAL_USER_ID:
                        $sVar = 'default_user';
                        break;
                    default:

                        break;
                }

                $aRow['value_actual'] = $aRow[$sVar];
            } else {
                $aRow['value_actual'] = $aRow[$sVar];
            }
        }

        switch ($aRow['type_id']) {
            case 'boolean':
                if (strtolower($aRow['value_actual']) == 'true'
                    || strtolower($aRow['value_actual']) == 'false'
                ) {
                    $aRow['value_actual'] = (strtolower($aRow['value_actual'])
                    == 'true' ? '1' : '0');
                }
                settype($aRow['value_actual'], 'boolean');
                break;
            case 'integer':
                settype($aRow['value_actual'], 'integer');
                break;
            case 'array':
            case 'multi_text':
            case 'currency':
                $aRow['value_actual'] = Phpfox::getLib('setting')
                    ->getActualValue($aRow['type_id'], $aRow['value_actual'], $aRow);
                break;
        }

        return $aRow;
    }

    public function addAlias($alias)
    {
        foreach ($alias as $key => $name) {
            $this->_aAlias[$key] = $name;
        }
        return $this;
    }

    /**
     * init params name alias
     * <usage>
     * call
     * Phpfox::getService('user.group.setting')->addAlias([alias:actual])
     * </usage>
     *
     * @see User_Service_Group_Setting_Setting::addAlias()
     */
    public function loadAlias()
    {

        if ($this->_loadAlias) {
            return;
        }

        $this->_loadAlias = true;

        if ($sPlugin
            = Phpfox_Plugin::get('user.service_group_setting_params_names')
        ) {
            eval($sPlugin);
        }
    }

    public function _setParam($iGroupId)
    {
        $sCacheId = $this->cache()->set('user_group_setting_' . $iGroupId);

        if (!($aParams = $this->cache()->get($sCacheId))) {

            // good for performance, does not query Phpfox::isAppAlias() too many times.
            // when we have an apps with the same alias, active apps will be loaded by calculator.

            /** @var array $appIds */
            $appIds = Phpfox::getAppIds();

            /** @var array $excludes */
            $excludes = Phpfox::getExcludeSettingsConditions();

            switch ($iGroupId) {
                case ADMIN_USER_ID:
                    $sVar = 'default_admin';
                    break;
                case GUEST_USER_ID:
                    $sVar = 'default_guest';
                    break;
                case STAFF_USER_ID:
                    $sVar = 'default_staff';
                    break;
                case NORMAL_USER_ID:
                    $sVar = 'default_user';
                    break;
                default:

                    break;
            }

            if (!isset($sVar)) {
                $sVar = 'default_value';

                $this->database()->select('ugc.default_value, inherit_id, ')
                    ->leftJoin(Phpfox::getT('user_group_custom'), 'ugc',
                        'ugc.user_group_id = ' . (int)$iGroupId
                        . ' AND ugc.module_id = user_group_setting.module_id AND ugc.name = user_group_setting.name')
                    ->join(Phpfox::getT('user_group'), 'ug',
                        'ug.user_group_id = ' . (int)$iGroupId);
            }

            $aRows = $this->database()
                ->select('m.module_id, user_group_setting.name, user_group_setting.product_id, user_group_setting.type_id, user_group_setting.default_admin, user_group_setting.default_user, user_group_setting.default_guest, user_group_setting.default_staff, user_setting.value_actual AS value_actual')
                ->from($this->_sTable, 'user_group_setting')
                ->leftJoin(Phpfox::getT('module'), 'm',
                    'm.module_id = user_group_setting.module_id')
                ->leftJoin(Phpfox::getT('user_setting'), 'user_setting',
                    "user_setting.user_group_id = '" . $iGroupId
                    . "' AND user_setting.setting_id = user_group_setting.setting_id")
                ->execute('getSlaveRows');

            $aParams = [];
            foreach ($aRows as $aRow) {

                $moduleId  = $aRow['module_id'];
                if(isset($excludes[$moduleId]) and $excludes[$moduleId] == $aRow['product_id'])
                    continue;

                $this->_setType($aRow, $sVar);

                $key = (Phpfox::isModule($aRow['module_id'])
                        ? $aRow['module_id'] . '.' : '') . $aRow['name'];

                // check is app alias
                if (in_array($aRow['product_id'], $appIds)) {
                    $aParams[$aRow['name']] = $aRow['value_actual'];
                }

                $aParams[$key] = $aRow['value_actual'];
            }

            // do not saved user_group_settings from app to `user_group_custom` anymore.
            ksort($aParams);

            $this->cache()->save($sCacheId, $aParams);
            Phpfox::getLib('cache')->group(  'user', $sCacheId);
        }

        return $aParams;
    }

    /**
     * Gets a list of activity points for editing.
     * Things to consider:
     *        There are default values (phpfox_user_group_setting)
     *        Changes to the default values are stored in phpfox_user_setting
     *        For custom user groups there may not be current values as they
     *        inherit from another user group This function works like this:
     *        Get a list of the default activity points to use their setting_id
     *        to find any override values if an override value is found then
     *        update the $aOut array return $aOut
     *
     * @param int $iUserGroup
     *
     * @return array|bool
     */
    public function getActivityPoints($iUserGroup)
    {
        $mValid = Phpfox::getService('user.group')->get([
            'user_group_id = ' . (int)$iUserGroup,
        ]);
        if (empty($mValid)) {
            return Phpfox_Error::set(_p('invalid_user_group'));
        }
        $mValid = $mValid[0];
        $aModules = Phpfox::massCallback('getDashboardActivity');

        $sGroup = '';

        /* if this user group inherits then take the default value of the parent user group*/
        switch ($mValid['inherit_id'] != 0 ? $mValid['inherit_id']
            : $iUserGroup) {
            case 1:
                $sGroup = 'default_admin';
                break;
            case 2:
                $sGroup = 'default_user';
                break;
            case 3:
                $sGroup = 'default_guest';
                break;
            case 4:
                $sGroup = 'default_staff';
                break;
        }

        $aOut = [];
        /* get default values */
        $sIn = '';
        foreach ($aModules as $sModule => $aModule) {
            $sIn .= '"points_' . $sModule . '",';
        }
        $sIn = rtrim($sIn, ',');
        $aDefaultSettings = $this->database()->select('*')
            ->from(Phpfox::getT('user_group_setting'), 'ugs')
            ->where('ugs.name IN (' . $sIn . ')')
            ->execute('getSlaveRows');

        /* get the current values */
        $sIn = '';
        foreach ($aDefaultSettings as $iKey => $aSetting) {
            $sIn .= $aSetting['setting_id'] . ',';
        }
        $sIn = rtrim($sIn, ',');
        $aCurrentSettings = $this->database()->select('*')
            ->from(Phpfox::getT('user_setting'))
            ->where('setting_id IN (' . $sIn . ') AND user_group_id = '
                . ((int)$iUserGroup))
            ->execute('getSlaveRows');
        /* Merge arrays */
        foreach ($aDefaultSettings as $iKey => $aDefault) {
            $aOut[$iKey] = [
                'setting_id'   => $aDefault['setting_id'],
                'name'         => $aDefault['name'],
                'module'       => $aDefault['module_id'],
                'value_actual' => $aDefault[$sGroup],
            ];

            /* if there a current setting, override the default value*/
            foreach ($aCurrentSettings as $aCurrent) {
                if ($aCurrent['setting_id'] == $aDefault['setting_id']) {
                    $aOut[$iKey]['value_actual'] = $aCurrent['value_actual'];
                }
            }
        }
        return $aOut;
    }
}
