<?php

namespace Apps\PHPfox_Videos\Installation\Version;

use Core\Lib as Lib;

class v453
{

    public function __construct()
    {
    }

    public function process()
    {
        $aMetaKeys = [
            'video_meta_description' => array(
                'description' => '<title>Video Meta Description</title><info>Meta description added to pages related to the Video app. <a target="_bank" href="' . \Phpfox_Url::instance()->makeUrl('admincp.language.phrase') . '?q=seo_video_meta_description">Click here</a> to edit meta description.<span style="float:right;">(SEO) <input style="width:150px;" readonly value="seo_video_meta_description"></span></info>',
                'meta_value' => 'Share your video with friends, family, and the world on Site Name.',
                'var_name' => 'pf_video_meta_description'
            ),
            'video_meta_keywords' => array(
                'description' => '<title>Video Meta Keywords</title><info>Meta keywords that will be displayed on sections related to the Video app. <a target="_bank" href="' . \Phpfox_Url::instance()->makeUrl('admincp.language.phrase') . '?q=seo_video_meta_keywords">Click here</a> to edit meta keywords.<span style="float:right;">(SEO) <input style="width:150px;" readonly value="seo_video_meta_keywords"></span></info>',
                'meta_value' => 'video, sharing, free, upload',
                'var_name' => 'pf_video_meta_keywords'
            ),
        ];
        // We'll update only one time
        foreach ($aMetaKeys as $sSetting => $aMetaKey) {
            $sNewPhrase = 'seo_' . $sSetting;
            if (!Lib::phrase()->isPhrase($sNewPhrase)) {
                if (Lib::phrase()->isPhrase($sSetting)) {
                    Lib::phrase()->clonePhrase($sSetting, $sNewPhrase);
                    // Update setting value
                    db()->update(':setting', array(
                        'value_actual' => '{_p var=\'' . $sNewPhrase . '\'}',
                        'value_default' => '{_p var=\'' . $sNewPhrase . '\'}',
                        'type_id' => '',
                        'group_id' => 'seo'
                    ), 'module_id=\'v\' AND var_name=\'' . $aMetaKey['var_name'] . '\'');

                    // Update setting description
                    db()->update(':language_phrase', array(
                        'text' => $aMetaKey['description'],
                        'text_default' => $aMetaKey['description']
                    ), 'var_name = \'' . $sSetting . '\'');
                } else {
                    $sValue = $aMetaKey['meta_value'];
                    Lib::phrase()->addPhrase($sNewPhrase, $sValue);
                }
            }
        }
    }
}
