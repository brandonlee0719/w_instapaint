<?php

namespace Apps\Core_RSS\Service\Group;

use Phpfox;
use Phpfox_Plugin;
use Phpfox_Error;
use Phpfox_Service;

class Process extends Phpfox_Service
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('rss_group');
    }

    public function add($aVals, $iUpdateId = null)
    {
        $aForm = array(
            'product_id' => array(
                'message' => _p('select_a_product'),
                'type' => 'product_id:required'
            ),
            'module_id' => array(
                'message' => _p('select_a_module'),
                'type' => 'module_id:required'
            ),
            'name_var' => array(
                'message' => _p('at_least_one_name_for_the_group_is_required'),
                'type' => 'phrase:required'
            ),
            'is_active' => array(
                'message' => _p('select_if_the_group_is_active_or_not'),
                'type' => 'int:required'
            )
        );

        $aLanguages = Phpfox::getService('language')->getAll();
        $sName = 'name_var';
        foreach ($aLanguages as $aLanguage) {
            if (!empty($aVals[$sName . '_' . $aLanguage['language_id']])) {
                $aVals[$sName][$aLanguage['language_id']] = $aVals[$sName . '_' . $aLanguage['language_id']];
                unset($aVals[$sName . '_' . $aLanguage['language_id']]);
            } else {
                Phpfox_Error::set(_p('provide_a_language_name_name_var',
                    ['language_name' => $aLanguage['title'], 'name_var' => ucwords(str_replace('_', ' ', $sName))]));
            }
        }

        if ($iUpdateId !== null) {
            unset($aForm['product_id'], $aForm['module_id']);

            $aVals = $this->validator()->process($aForm, $aVals);

            if (!Phpfox_Error::isPassed()) {
                return false;
            }

            $aPhrases['name_var'] = $aVals['name_var'];
            $sPhraseName = 'rss_group_name_' . $iUpdateId;
            unset($aVals['name_var']);

            db()->update($this->_sTable, $aVals, 'group_id = ' . $iUpdateId);
            foreach ($aLanguages as $aLanguage) {
                if (isset($aPhrases[$sName][$aLanguage['language_id']])) {
                    Phpfox::getService('language.phrase.process')->updateVarName($aLanguage['language_id'],
                        $sPhraseName, $aPhrases[$sName][$aLanguage['language_id']]);
                }
            }

            $this->cache()->remove();
        } else {
            $aVals = $this->validator()->process($aForm, $aVals);

            if (!Phpfox_Error::isPassed()) {
                return false;
            }

            $aPhrases = $aVals['name_var'];
            unset($aVals['name_var']);

            $iId = db()->insert($this->_sTable, $aVals);

            $sPhraseVar = Phpfox::getService('language.phrase.process')->add([
                'var_name' => 'rss_group_name_' . $iId,
                'text' => $aPhrases
            ]);

            db()->update($this->_sTable, array('name_var' => $sPhraseVar), 'group_id = ' . $iId);
        }

        return true;
    }

    public function update($iId, $aVals)
    {
        return $this->add($aVals, $iId);
    }

    public function updateActivity($iId, $iType)
    {
        Phpfox::isUser(true);
        Phpfox::getUserParam('admincp.has_admin_access', true);

        db()->update($this->_sTable, array('is_active' => (int)($iType == '1' ? 1 : 0)),
            'group_id = ' . (int)$iId);

        $this->cache()->remove('rss', 'substr');
    }

    public function updateOrder($aVals)
    {
        Phpfox::isUser(true);
        Phpfox::getUserParam('admincp.has_admin_access', true);

        if (!isset($aVals['ordering'])) {
            return Phpfox_Error::set(_p('not_a_valid_request'));
        }

        foreach ($aVals['ordering'] as $iId => $iOrder) {
            db()->update($this->_sTable, array('ordering' => (int)$iOrder), 'group_id = ' . (int)$iId);
        }

        $this->cache()->remove('rss', 'substr');
        return null;
    }

    public function delete($iId)
    {
        Phpfox::isUser(true);
        Phpfox::getUserParam('admincp.has_admin_access', true);

        $aGroup = db()->select('group_id, module_id')
            ->from($this->_sTable)
            ->where('group_id = ' . (int)$iId)
            ->execute('getSlaveRow');

        if (!isset($aGroup['group_id'])) {
            return Phpfox_Error::set(_p('the_group_you_are_looking_for_cannot_be_found'));
        }

        db()->delete($this->_sTable, 'group_id = ' . $aGroup['group_id']);
        db()->delete(Phpfox::getT('language_phrase'),
            'module_id = \'' . $aGroup['module_id'] . '\' AND var_name = \'rss_group_name_' . $aGroup['group_id'] . '\'');

        $this->cache()->remove('stat', 'substr');

        return true;
    }


    /**
     * If a call is made to an unknown method attempt to connect
     * it to a specific plug-in with the same name thus allowing
     * plug-in developers the ability to extend classes.
     *
     * @param string $sMethod is the name of the method
     * @param array $aArguments is the array of arguments of being passed
     */
    public function __call($sMethod, $aArguments)
    {
        /**
         * Check if such a plug-in exists and if it does call it.
         */
        if ($sPlugin = Phpfox_Plugin::get('rss.service_group_process__call')) {
            eval($sPlugin);
            return null;
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
    }
}