<?php

namespace Apps\Core_RSS\Service;

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
        $this->_sTable = Phpfox::getT('rss');
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
            'group_id' => array(
                'message' => _p('select_a_group_for_this_feed'),
                'type' => 'int:required'
            ),
            'title_var' => array(
                'message' => _p('at_least_one_title_for_the_feed_is_required'),
                'type' => 'phrase:required'
            ),
            'description_var' => array(
                'message' => _p('at_least_one_description_for_the_feed_is_required'),
                'type' => 'phrase:required'
            ),
            'feed_link' => array(
                'message' => _p('provide_a_link_for_the_feed'),
                'type' => 'string:required'
            ),
            'php_group_code' => array(
                'message' => _p('provide_proper_php_code'),
                'type' => 'php_code'
            ),
            'php_view_code' => array(
                'message' => _p('php_code_for_the_feed_is_required'),
                'type' => 'php_code:required'
            ),
            'is_site_wide' => array(
                'message' => _p('select_if_the_feed_can_be_seen_site_wide'),
                'type' => 'int:required'
            ),
            'is_active' => array(
                'message' => _p('select_if_the_feed_is_active_or_not'),
                'type' => 'int:required'
            )
        );

        $aLanguages = Phpfox::getService('language')->getAll();
        $aFields = array('title_var', 'description_var');
        foreach ($aFields as $sName) {
            foreach ($aLanguages as $aLanguage) {
                if (!empty($aVals[$sName . '_' . $aLanguage['language_id']])) {
                    $aVals[$sName][$aLanguage['language_id']] = $aVals[$sName . '_' . $aLanguage['language_id']];
                    unset($aVals[$sName . '_' . $aLanguage['language_id']]);
                } else {
                    Phpfox_Error::set(_p('provide_a_language_name_name_var', ['language_name' => $aLanguage['title'], 'name_var' => ucwords(str_replace('_', ' ', $sName))]));
                }
            }
        }
        if ($iUpdateId !== null) {
            unset($aForm['product_id'], $aForm['module_id']);

            $aVals = $this->validator()->process($aForm, $aVals);

            if (!Phpfox_Error::isPassed()) {
                return false;
            }

            $aPhrases['title_var'] = $aVals['title_var'];
            $aPhrases['description_var'] = $aVals['description_var'];
            $aVarNames = array('rss_title_' . $iUpdateId, 'rss_description_' . $iUpdateId);
            unset($aVals['title_var'], $aVals['description_var']);

            db()->update($this->_sTable, $aVals, 'feed_id = ' . $iUpdateId);

            foreach ($aFields as $iKey => $sName) {
                foreach ($aLanguages as $aLanguage) {
                    if (isset($aPhrases[$sName][$aLanguage['language_id']])) {
                        Phpfox::getService('language.phrase.process')->updateVarName($aLanguage['language_id'],
                            $aVarNames[$iKey], $aPhrases[$sName][$aLanguage['language_id']]);
                    }
                }
            }
        } else {
            $aVals = $this->validator()->process($aForm, $aVals);

            if (!Phpfox_Error::isPassed()) {
                return false;
            }

            $aPhrases = $aVals['title_var'];
            $aDescriptions = $aVals['description_var'];
            unset($aVals['title_var'], $aVals['description_var']);

            $iId = db()->insert($this->_sTable, $aVals);

            $sPhraseVar = Phpfox::getService('language.phrase.process')->add([
                'var_name' => 'rss_title_' . $iId,
                'text' => $aPhrases
            ]);

            $sDescriptionVar = Phpfox::getService('language.phrase.process')->add([
                'var_name' => 'rss_description_' . $iId,
                'text' => $aDescriptions
            ]);

            db()->update($this->_sTable,
                array('title_var' => $sPhraseVar, 'description_var' => $sDescriptionVar), 'feed_id = ' . $iId);
        }

        $this->cache()->remove();

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
            'feed_id = ' . (int)$iId);

        $this->cache()->remove('rss', 'substr');
    }

    public function updateSiteWide($iId, $iType)
    {
        Phpfox::isUser(true);
        Phpfox::getUserParam('admincp.has_admin_access', true);

        db()->update($this->_sTable, array('is_site_wide' => (int)($iType == '1' ? 1 : 0)),
            'feed_id = ' . (int)$iId);

        $this->cache()->remove('rss', 'substr');
    }

    public function delete($iId)
    {
        Phpfox::isUser(true);
        Phpfox::getUserParam('admincp.has_admin_access', true);

        $aFeed = db()->select('feed_id, module_id')
            ->from($this->_sTable)
            ->where('feed_id = ' . (int)$iId)
            ->executeRow();

        if (!isset($aFeed['feed_id'])) {
            return Phpfox_Error::set(_p('the_feed_you_are_looking_for_cannot_be_found'));
        }

        db()->delete($this->_sTable, 'feed_id = ' . $aFeed['feed_id']);
        db()->delete(Phpfox::getT('language_phrase'),
            'module_id = \'' . $aFeed['module_id'] . '\' AND var_name = \'rss_title_' . $aFeed['feed_id'] . '\'');
        db()->delete(Phpfox::getT('language_phrase'),
            'module_id = \'' . $aFeed['module_id'] . '\' AND var_name = \'rss_description_' . $aFeed['feed_id'] . '\'');

        $this->cache()->remove('rss', 'substr');

        return true;
    }

    public function updateOrder($aVals)
    {
        Phpfox::isUser(true);
        Phpfox::getUserParam('admincp.has_admin_access', true);

        if (!isset($aVals['ordering'])) {
            return Phpfox_Error::set(_p('not_a_valid_request'));
        }

        foreach ($aVals['ordering'] as $iId => $iOrder) {
            db()->update($this->_sTable, array('ordering' => (int)$iOrder), 'feed_id = ' . (int)$iId);
        }

        $this->cache()->remove('rss', 'substr');
        return null;
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
        if ($sPlugin = Phpfox_Plugin::get('rss.service_process__call')) {
            eval($sPlugin);
            return null;
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
    }
}
