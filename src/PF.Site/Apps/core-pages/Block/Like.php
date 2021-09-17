<?php

namespace Apps\Core_Pages\Block;

use Phpfox;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

class Like extends \Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        $iLimit = $this->getParam('limit', 4);
        if (!$iLimit) {
            return false;
        }

        $aPage = $this->getParam('aPage');
        list($iLikeCount,) = Phpfox::getService('pages')->getMembers($aPage['page_id']);

        if (!$iLikeCount) {
            return false;
        }

        list(, $aMembers) = Phpfox::getService('pages')->getMembers($aPage['page_id'], $iLimit);
        $this->template()->assign(array(
                'sHeader' => $aPage['page_type'] == '1' ? _p('members') : _p('likes'),
                'aMembers' => $aMembers,
                'bShowFriendInfo' => true
            )
        );
        if ($iLikeCount > $iLimit) {
            $this->template()->assign([
                'aFooter' => [
                    _p('more') => [
                        'link' => 'javascript:void(0)',
                        'attr' => 'onclick="$Core.box(\'like.browse\', 400, \'type_id=pages&amp;item_id=' . $aPage['page_id'] . '' . ($aPage['page_type'] != '1' ? '&amp;force_like=1' : '') . '\');"'
                    ]
                ]
            ]);
        }
        $this->setParam([
            'mutual_list' => true
        ]);

        return 'block';
    }

    /**
     * @return array
     */
    public function getSettings()
    {
        return [
            [
                'info' => _p('block_like_info'),
                'description' => _p('block_like_description'),
                'value' => 4,
                'var_name' => 'limit',
                'type' => 'integer'
            ]
        ];
    }

    /**
     * Validation
     *
     * @return array
     */
    public function getValidation()
    {
        return [
            'limit' => [
                'def' => 'int:required',
                'min' => 0,
                'title' => _p('limit_must_greater_or_equal_0')
            ]
        ];
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('pages.component_block_like_clean')) ? eval($sPlugin) : false);
    }
}
