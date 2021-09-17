<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 *
 * @copyright        [PHPFOX_COPYRIGHT]
 * @author           Raymond Benc
 * @package          Phpfox_Component
 * @version          $Id: sample.class.php 1297 2009-12-04 23:18:17Z
 *                   Raymond_Benc $
 */
class Language_Component_Block_Admincp_Multiple extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        $aLanguages = Phpfox::getLib('template')->getVar('aLanguages');

        if(empty($aLanguages)){
            $aLanguages  = Phpfox::getService('language')->getAll(true);
        }

        $sPhraseName = $this->getParam('phrase');
        $sPhraseValue = '';

        $aForms = Phpfox::getLib('template')->getVar('aForms');
        if(is_array($aForms) and array_key_exists($sPhraseName, $aForms)){
            $sPhraseValue =  $aForms[$sPhraseName];
        }
        if(null == $sPhraseValue){
            $sPhraseValue = Phpfox::getLib('template')->getVar($sPhraseName);
        }

        $languages = $aLanguages;
        $aDefault = array_shift($languages);

        $aTranslatedPhraseValues = [];

        foreach ($aLanguages as $aLanguage) {
            $aTranslatedPhraseValues[$aLanguage['language_id']] = $sPhraseValue ? _p($sPhraseValue, [], $aLanguage['language_id']) : '';
        }

        $this->template()->assign([
                'aTranslatedPhraseValues'       => $aTranslatedPhraseValues,
                'sDefaultTranslatedPhraseValue' => $aTranslatedPhraseValues[$aDefault['language_id']],
                'aDefaultLanguage'              => $aDefault,
                'aOtherLanguages'               => $languages,
                'bRequired'                     => $this->getParam('required', false),
                'sLabel'                        => _p($this->getParam('label', 'name')),
                'sField'                        => $this->getParam('field', 'name'),
                'sMaxLength'                    => $this->getParam('maxlength', '200'),
                'sType'                         => $this->getParam('type', 'textarea'),
                'sFormat'                       => $this->getParam('format', 'name_'),
                'sSize'                         => $this->getParam('size', '30'),
                'sRows'                         => $this->getParam('rows', '5'),
                'sCachePhrase'                  => $this->request()->get('phrase'),
                'sHelpPhrase'                   => $this->getParam('help_phrase', 'if_the_category_is_empty_then_its_value_will_have_the_same_value_as_default_language'),
            ]
        );
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin
            = Phpfox_Plugin::get('language.component_block_sample_clean'))
            ? eval($sPlugin) : false);
    }
}