<?php

namespace Apps\Core_Polls\Installation\Version;

use Core\Lib as Lib;
use Phpfox;

class v460
{
    public function __construct()
    {

    }

    public function process()
    {
        // add activity field
        if (!db()->isField(':user_activity', 'activity_poll')) {
            db()->query("ALTER TABLE  `" . Phpfox::getT('user_activity') . "` ADD `activity_poll` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0'");
        }

        // add statistics total blog field
        if (!db()->isField(':user_field', 'total_poll')) {
            db()->query("ALTER TABLE  `" . Phpfox::getT('user_field') . "` ADD `total_poll` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0'");
        }
        // remove settings
        db()->delete(':setting', 'module_id="poll" AND var_name="poll_view_time_stamp"');
        db()->delete(':user_group_setting', 'module_id="poll" AND name="can_edit_title"');
        db()->delete(':user_group_setting', 'module_id="poll" AND name="can_edit_question"');
        db()->delete(':user_group_setting', 'module_id="poll" AND name="can_view_hidden_poll_votes"');
        //delete menu
        db()->delete(':menu', "`module_id` = 'poll' AND `url_value` = 'poll.add'");
        // add description
        if (!db()->isField(':poll', 'description')) {
            db()->query("ALTER TABLE  `" . Phpfox::getT('poll') . "` ADD `description` MEDIUMTEXT");
        }
        if (!db()->isField(':poll', 'description_parsed')) {
            db()->query("ALTER TABLE  `" . Phpfox::getT('poll') . "` ADD `description_parsed` MEDIUMTEXT");
        }
        if (!db()->isField(':poll', 'total_attachment')) {
            db()->query("ALTER TABLE  `" . Phpfox::getT('poll') . "` ADD `total_attachment` INT(10) UNSIGNED NOT NULL DEFAULT '0'");
        }
        if (!db()->isField(':poll', 'is_multiple')) {
            db()->query("ALTER TABLE  `" . Phpfox::getT('poll') . "` ADD `is_multiple` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0'");
        }

        // update module is app
        db()->update(':module', ['phrase_var_name' => 'module_apps', 'is_active' => 1], ['module_id' => 'poll']);

        $aMetaKeys = [
            'poll_meta_description' => array(
                'description' => '<title>Poll Meta Description</title><info>Meta description added to pages related to the Polls app. <a target="_bank" href="' . \Phpfox_Url::instance()->makeUrl('admincp.language.phrase') . '?q=poll_meta_description">Click here</a> to edit meta description.<span style="float:right;">(SEO) <input style="width:150px;" readonly value="poll_meta_description"></span></info>',
                'meta_value' => 'Share your polls with friends, family, and the world on Site Name.c'
            ),
            'poll_meta_keywords' => array(
                'description' => '<title>Poll Meta Keywords</title><info>Meta keywords that will be displayed on sections related to the Polls app. <a target="_bank" href="' . \Phpfox_Url::instance()->makeUrl('admincp.language.phrase') . '?q=poll_meta_keywords">Click here</a> to edit meta keywords.<span style="float:right;">(SEO) <input style="width:150px;" readonly value="poll_meta_keywords"></span></info>',
                'meta_value' => 'poll, answer, free, question'
            ),
        ];
        // We'll update only one time
        foreach ($aMetaKeys as $sSetting => $aMetaKey) {
            $sNewPhrase = 'seo_' . $sSetting;
            if (!Lib::phrase()->isPhrase($sNewPhrase)) {
                $sValue = Phpfox::getParam('poll.' . $sSetting);

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
                ), 'module_id=\'poll\' AND var_name=\'' . $sSetting . '\'');

                // Update setting description
                db()->update(':language_phrase', array(
                    'text' => $aMetaKey['description'],
                    'text_default' => $aMetaKey['description']
                ), 'var_name = \'' . $sSetting . '\'');
            }
        }
    }
}