<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 *
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Language
 * @version 		$Id: phrase.class.php 5538 2013-03-25 13:20:22Z Miguel_Espinoza $
 */
class Language_Component_Controller_Admincp_Phrase_Phrase extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        Phpfox::getUserParam('language.can_manage_lang_packs', true);

        $iPage = $this->request()->getInt('page');
        $oPhraseProcess = Phpfox::getService('language.phrase.process');
        $oCache = Phpfox::getLib('cache');

        if ($this->request()->get('save') && ($aTexts = $this->request()->getArray('text')))
        {
            foreach ($aTexts as $iKey => $sText)
            {
                $oPhraseProcess->update($iKey, $sText);
            }

            Phpfox::getLib('cache')->removeGroup('locale');

            $oCache->remove('apps_header_scripts');

            $this->url()->send('current', null, _p('phrase_s_updated'));
        }

        if ($this->request()->get('save_selected') && ($aTexts = $this->request()->getArray('text')) && ($aIds = $this->request()->getArray('id')))
        {
            foreach ($aTexts as $iKey => $sText)
            {
                if (!in_array($iKey, $aIds))
                {
                    continue;
                }
                $oPhraseProcess->update($iKey, $sText);
            }

            Phpfox::getLib('cache')->removeGroup('locale');

            $this->url()->send('current', null, _p('phrase_s_updated'));
        }

        if ($this->request()->get('revert_selected') && ($aIds = $this->request()->getArray('id')))
        {
            if ($oPhraseProcess->revert($aIds))
            {
                Phpfox::getLib('cache')->removeGroup('locale');
                $this->url()->send('current', null, _p('selected_phrase_s_successfully_reverted'));
            }
        }

        if ($this->request()->get('delete') && ($aIds = $this->request()->getArray('id')))
        {
            foreach ($aIds as $iId)
            {
                $oPhraseProcess->delete($iId);
            }

            Phpfox::getLib('cache')->removeGroup('locale');

            $this->url()->send('current', null, _p('selected_phrase_s_successfully_deleted'));
        }

        $aLanguages = Phpfox::getService('language')->get();
        $aLangs = array();
        foreach ($aLanguages as $aLanguage)
        {
            $aLangs[$aLanguage['language_id']] = $aLanguage['title'];
        }

        $aPages = array(20, 40, 60, 80, 100);
        $aDisplays = array();
        foreach ($aPages as $iPageCnt)
        {
            $aDisplays[$iPageCnt] = _p('per_page', array('total' => $iPageCnt));
        }

        $aSorts = array(
            'added' => _p('time'),
            'phrase_id' => _p('phrase_id')
        );

        $aFilters = array(
            'display' => array(
                'type' => 'select',
                'options' => $aDisplays,
                'default' => '20'
            ),
            'sort' => array(
                'type' => 'select',
                'options' => $aSorts,
                'default' => 'added',
                'alias' => 'lp'
            ),
            'sort_by' => array(
                'type' => 'select',
                'options' => array(
                    'DESC' => _p('descending'),
                    'ASC' => _p('ascending')
                ),
                'default' => 'DESC'
            ),
            'language_id' => array(
                'type' => 'select',
                'options' => $aLangs,
                'add_select' => true,
                'search' => "AND lp.language_id = '[VALUE]'",
                'id' => 'js_language_id'
            ),
            'translate_type' => array(
                'type' => 'select',
                'options' => array(
                    '0' => _p('all_phrases'),
                    '1' => _p('not_translated'),
                    '2' => _p('translated_only'),
                )
            ),
            'search' => array(
                'type' => 'input:text',
            ),
            'search_type' => array(
                'type' => 'input:radio',
                'options' => array(
                    '0' => array(_p('phrase_text_only'), "AND lp.text LIKE '%[VALUE]%'"),
                    '1' => array(_p('phrase_variable_name_only'), "AND lp.var_name LIKE '%[VALUE]%'"),
                    '2' => array(_p('phrase_text_and_phrase_variable_name'), "AND (lp.text LIKE '%[VALUE]%' OR lp.var_name LIKE '%[VALUE]%')")
                ),
                'depend' => 'search',
                'prefix' => '<div>',
                'suffix' => '</div>',
                'default' => '0'
            )
        );

        $oSearch = Phpfox_Search::instance()->set(array(
            'type' => 'phrases',
            'cache' => true,
            'filters' => $aFilters,
            'field' => 'lp.phrase_id',
            'search' => 'search'
        ));

        if ($oSearch->isSearch())
        {
            if (!defined('PHPFOX_SEARCH_MODE_CONVERT')) {
                define('PHPFOX_SEARCH_MODE_CONVERT', true);
            }
            $aResults = Phpfox::getService('language.phrase')->getSearch($oSearch->getConditions(), $oSearch->getSort());
            if (count($aResults))
            {
                $oSearch->cacheResults('search', $aResults);
            }
        }

        $bIsForceLanguagePackage = false;
        if ($iLangId = $this->request()->get('lang-id'))
        {
            $bIsForceLanguagePackage = true;
            $oSearch->setCondition('AND lp.language_id = \'' . Phpfox_Database::instance()->escape($iLangId) . '\'');
            $this->template()->setHeader('<script type="text/javascript">$Behavior.language_admincp_phrase = function(){ $(\'#js_language_id\').val(\'' . $iLangId. '\'); };</script>');
        }

        if (empty($iLangId) && ($iLangId = $oSearch->get('language_id')))
        {
        }

        if (($sTranslate = $oSearch->get('translate_type')))
        {
            if ($sTranslate == '1')
            {
                $oSearch->setCondition(' AND lp.text = lp.text_default');
            }
            elseif ($sTranslate == '2')
            {
                $oSearch->setCondition(' AND lp.text != lp.text_default');
            }
        }

        $iPageSize = $oSearch->getDisplay();

        if (!defined('PHPFOX_SEARCH_MODE_CONVERT')) {
            define('PHPFOX_SEARCH_MODE_CONVERT', true);
        }

        list($iCnt, $aRows) = Phpfox::getService('language.phrase')->get($oSearch->getConditions(true), $oSearch->getSort(), $iPage, $iPageSize);

        $cache = [];
        $oSearchOutput = Phpfox::getLib('parse.output');
        $aOut = array();
        foreach ($aRows as $iKey => $aRow)
        {
            if (!isset($cache[$aRow['language_id']])) {
                $cache[$aRow['language_id']] = [];
            }

            if (isset($cache[$aRow['language_id']][$aRow['var_name']])) {
                \Phpfox_Database::instance()->delete(':language_phrase', ['phrase_id' => $aRow['phrase_id']]);
                continue;
            }

            $aOut[$aRow['phrase_id']] = $aRow;
            $aOut[$aRow['phrase_id']]['sample_text'] = $oSearch->highlight('search', $oSearchOutput->htmlspecialchars($aRow['text_default']));
            $aOut[$aRow['phrase_id']]['is_translated'] = (md5($aRow['text_default']) != md5($aRow['text']) ? true : false);
        }
        $aRows = $aOut;
        Phpfox_Pager::instance()->set(array('page' => $iPage, 'size' => $iPageSize, 'count' => $iCnt));

        //Admin can add new phrase without define PHPFOX_IS_TECHIE
        $this->template()->setActionMenu([
            _p('new_phrase') => [
                'url' => $this->url()->makeUrl('admincp.language.phrase.add'),
                'class' => 'popup'
            ]
        ]);
        $this->template()->assign(array(
            'bShowClearCache'=>true,
            'aRows' => $aRows,
            'iPage' => $iPage,
            'sSearchId' => $this->request()->get('search-rid'),
            'sSearchIdNormal' => $this->request()->get('search-id'),
            'iLangId' => $iLangId,
            'bIsForceLanguagePackage' => $bIsForceLanguagePackage
        ))->setSectionTitle(_p('phrases'))
            ->setTitle(_p('phrase_manager'))
            ->setActiveMenu('admincp.globalize.phrase');

        if ($this->request()->get('q')) {
            $this->template()->assign('q', $this->request()->get('q'));
        }
    }
}