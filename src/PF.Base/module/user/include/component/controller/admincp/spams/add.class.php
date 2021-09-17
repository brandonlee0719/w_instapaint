<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class User_Component_Controller_Admincp_Spams_Add
 */
class User_Component_Controller_Admincp_Spams_Add extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        $aQuestion = [];
        $iQuestionId = $this->request()->getInt('id');


        if (($aVals = $this->request()->getArray('val'))) {
            if ($iQuestionId) {
                $aVals['question_id'] = $iQuestionId;
                if (Phpfox::getService('user.process')->editSpamQuestion($aVals)) {
                    Phpfox::getLib('url')->send('admincp.user.spam');
                }
            } elseif (Phpfox::getService('user.process')->addSpamQuestion($aVals)) {
                Phpfox::getLib('url')->send('admincp.user.spam');
            }
        }


        if ($iQuestionId) {
            $aQuestion = Phpfox::getService('user')->getSpamQuestion($iQuestionId);
        } elseif (!empty($aVals)) {
            // populate form when submit unsuccessfully
            $aQuestion = [
                'question_phrase' => $aVals['question'],
                'answers_phrases' => isset($aVals['answer'])?$aVals['answer']:[]
            ];
        }

        $this->template()
            ->setBreadCrumb(_p('anti_spam_security_questions'))
            ->setTitle(_p('anti_spam_security_questions'))
            ->setSectionTitle(_p('anti_spam_questions'))
            ->setActiveMenu('admincp.settings.spam')
            ->assign([
                'aQuestion' => $aQuestion,
                'iQuestionId' => $iQuestionId,
                'sSiteUsePhrase' => $this->url()->makeUrl('admincp.language.phrase.add', ['last-module' => 'user']),
            ])
            ->setHeader([
                'admin.spam.js' => 'module_user',
            ])
            ->setPhrase([
                'setting_require_all_spam_questions_on_signup',
                'edit_question',
            ]);
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('user.component_controller_index_clean')) ? eval($sPlugin) : false);
    }
}
