<?php

namespace Apps\Core_Quizzes\Installation\Version;

use Phpfox;
use \Core\Lib as Lib;

class v460
{
    public function __construct()
    {

    }

    public function process()
    {
        //migrate total play
        //count all total play of old data
        $aAnswers = db()->select('qr.*')
            ->from(':quiz_result', 'qr')
            ->group('qr.quiz_id,qr.user_id')
            ->execute('getSlaveRows');
        $aTotalPlays = [];
        foreach ($aAnswers as $aAnswer) {
            if (isset($aTotalPlays[$aAnswer['quiz_id']])) {
                $aTotalPlays[$aAnswer['quiz_id']]++;
            } else {
                $aTotalPlays[$aAnswer['quiz_id']] = 1;
            }
        }
        foreach ($aTotalPlays as $iKey => $aTotalPlay) {
            db()->update(':quiz', ['total_play' => $aTotalPlay], 'quiz_id = ' . (int)$iKey);
        }

        // remove settings
        db()->delete(':setting', 'module_id="quiz" AND var_name="quizzes_to_show"');
        db()->delete(':setting', 'module_id="quiz" AND var_name="quiz_view_time_stamp"');
        db()->delete(':user_group_setting', 'module_id="quiz" AND name="can_edit_own_title"');
        db()->delete(':user_group_setting', 'module_id="quiz" AND name="can_edit_others_title"');

        // Update old settings
        $aSettingsTakenByBlock = array(
            'limit' => Phpfox::getParam('quiz.takers_to_show', 10),
            'cache_time' => Phpfox::getParam('core.cache_time_default'),
        );
        db()->update(':block', array('params' => json_encode($aSettingsTakenByBlock)),
            'component = \'stat\' AND module_id = \'quiz\' AND params IS NULL');
        db()->delete(':setting', 'module_id="quiz" AND var_name="takers_to_show"');

        //delete menu
        db()->delete(':menu', "`module_id` = 'quiz' AND `url_value` = 'quiz.add'");
        if (db()->isField(':quiz', 'description')) {
            db()->query("ALTER TABLE  `" . Phpfox::getT('quiz') . "` CHANGE COLUMN `description` `description` mediumtext default null");
        }
        if (db()->isField(':quiz', 'description_parsed')) {
            db()->query("ALTER TABLE  `" . Phpfox::getT('quiz') . "` CHANGE COLUMN `description_parsed` `description_parsed` mediumtext default null");
        }
        // add activity field
        if (!db()->isField(':user_activity', 'activity_quiz')) {
            db()->query("ALTER TABLE  `" . Phpfox::getT('user_activity') . "` ADD `activity_quiz` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0'");
        }

        // add statistics total blog field
        if (!db()->isField(':user_field', 'total_quiz')) {
            db()->query("ALTER TABLE  `" . Phpfox::getT('user_field') . "` ADD `total_quiz` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0'");
        }
        // update module is app
        db()->update(':module', ['phrase_var_name' => 'module_apps', 'is_active' => 1], ['module_id' => 'quiz']);

        $aMetaKeys = [
            'quiz_meta_description' => array(
                'description' => '<title>Quiz Meta Description</title><info>Meta description added to pages related to the Quizzes app. <a target="_bank" href="' . \Phpfox_Url::instance()->makeUrl('admincp.language.phrase') . '?q=quiz_meta_description">Click here</a> to edit meta description.<span style="float:right;">(SEO) <input style="width:150px;" readonly value="quiz_meta_description"></span></info>',
                'meta_value' => 'Take Free Fun Quizzes & Tests. Cool Online Fun Quiz & Test. Fun Quizzes and Fun Tests.'
            ),
            'quiz_meta_keywords' => array(
                'description' => '<title>Quiz Meta Keywords</title><info>Meta keywords that will be displayed on sections related to the Quizzes app. <a target="_bank" href="' . \Phpfox_Url::instance()->makeUrl('admincp.language.phrase') . '?q=quiz_meta_keywords">Click here</a> to edit meta keywords.<span style="float:right;">(SEO) <input style="width:150px;" readonly value="quiz_meta_keywords"></span></info>',
                'meta_value' => 'quizzes, fun, test, question'
            ),
        ];
        // We'll update only one time
        foreach ($aMetaKeys as $sSetting => $aMetaKey) {
            $sNewPhrase = 'seo_' . $sSetting;
            if (!Lib::phrase()->isPhrase($sNewPhrase)) {
                $sValue = Phpfox::getParam('quiz.' . $sSetting);

                // If old setting is formatted phrase already
                if (preg_match('/\{_p var=(.*)\}/i', $sValue, $aMatches)) {
                    Lib::phrase()->clonePhrase(trim($aMatches[1], '\'\"'), $sNewPhrase);
                } elseif (preg_match('/\{phrase var=(.*)\}/i', $sValue, $aMatches)) {
                    Lib::phrase()->clonePhrase(trim($aMatches[1], '\'\"'), $sNewPhrase);
                } else {
                    $sValue = $aMetaKey['meta_value'];
                    Lib::phrase()->addPhrase($sNewPhrase, $sValue);
                }

                // Update setting value
                db()->update(':setting', array(
                    'value_actual' => '{_p var=\'' . $sNewPhrase . '\'}',
                    'value_default' => '{_p var=\'' . $sNewPhrase . '\'}',
                    'type_id' => ''
                ), 'module_id=\'quiz\' AND var_name=\'' . $sSetting . '\'');

                // Update setting description
                db()->update(':language_phrase', array(
                    'text' => $aMetaKey['description'],
                    'text_default' => $aMetaKey['description']
                ), 'var_name = \'' . $sSetting . '\'');
            }
        }
    }
}