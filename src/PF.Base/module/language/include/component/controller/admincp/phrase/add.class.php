<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Language_Component_Controller_Admincp_Phrase_Add
 */
class Language_Component_Controller_Admincp_Phrase_Add extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        Phpfox::getUserParam('language.can_manage_lang_packs', true);

        $bNoJsValidation = $this->getParam('bNoJsValidation');
        $aLanguages = Phpfox::getService('language')->get();
        $validatorService = Phpfox::getLib('validator');
        $processService = Phpfox::getService('language.phrase.process');
        $phraseService = Phpfox::getService('language.phrase');
        if ($sPhrase = $this->getParam('sVar')) {
            $aParts = explode('.', $sPhrase);
            $sPhrase = $aParts[1];
        }

        $aValidation = [];

        $oValid = $validatorService->set(['sFormName' => 'js_phrase_form', 'aParams' => $aValidation]);

        if ($aVals = $this->request()->getArray('val')) {
            if ($aVals['type'] == 1) {
                if (empty($aVals['phrase'])) {
                    Phpfox_Error::set(_p('Provide the phrase.'));
                }
            } else {
                if (empty($aVals['var_name']) && isset($aVals['text']['en'])) {
                    $aVals['var_name'] = $aVals['text']['en'];
                }

                if (empty($aVals['var_name'])) {
                    Phpfox_Error::set(_p('provide_a_var_name_dot'));
                }
            }

            // Check that all the fields are valid
            if ($oValid->isValid($aVals)) {
                // Check to make sure the phrase has not already been added
                if (($sIsPhrase = $phraseService->isPhrase($aVals))) {
                    Phpfox_Error::set(_p('phrase_already_created', ['phrase' => $sIsPhrase]) . ' - ' . _p($sIsPhrase));

                    $sCachePhrase = $sIsPhrase;
                } else {


                    // Add the new phrase
                    $sPhrase = $processService->add($aVals);
                    $sVarName = $processService->prepare($sPhrase);
                    $sCached = _p('phrase_added', ['phrase' => $sVarName]);

                    // Verify if we have a return URL, if we do send them there instead
                    if (($sReturn = $this->request()->get('return'))) {
                        $this->url()->forward($sReturn, $sCached);
                    } else {
                        Phpfox::getLib('session')->set('cache_new_phrase', $sVarName);

                        // Phrase added lets send them back to the same page with a message that the phrase was added
                        $this->url()->send('admincp.language.phrase.add', [], $sCached);
                    }
                }
            }
        }

        if (!isset($sCachePhrase) && ($sCachePhrase = Phpfox::getLib('session')->get('cache_new_phrase'))) {
            Phpfox::getLib('session')->remove('cache_new_phrase');
        }

        // Assign needed vars to the template
        $this->template()->assign([
            'aLanguages'    => $aLanguages,
            'sCreateJs'     => $oValid->createJS(),
            'sGetJsForm'    => ($bNoJsValidation ? 'return true;' : $oValid->getJsForm()),
            'sReturn'       => (($sReturn = $this->request()->get('return')) ? $sReturn
                : $this->getParam('sReturnUrl')),
            'sVar'          => $sPhrase,
            'sCachePhrase'  => (isset($sCachePhrase) ? $sCachePhrase : ''),
            'sLastModuleId' => $this->request()->get('last-module'),
        ])
            ->setBreadCrumb(_p('add_phrase'))
            ->setTitle(_p('add_phrase'),true)
            ->setActiveMenu('admincp.globalize.phrase');

        (($sPlugin = Phpfox_Plugin::get('language.component_controller_admincp_phrase_add_process')) ? eval($sPlugin)
            : false);


    }

    /**
     * Clean memory if needed
     *
     */
    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('language.component_controller_admincp_phrase_add_clean')) ? eval($sPlugin)
            : false);
    }
}