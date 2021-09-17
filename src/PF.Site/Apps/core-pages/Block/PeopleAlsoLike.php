<?php

namespace Apps\Core_Pages\Block;

use Phpfox;

defined('PHPFOX') or exit('NO DICE!');

class PeopleAlsoLike extends \Phpfox_Component
{
    public function process()
    {
        $aPage = $this->getParam('aPage', false);
        $iLimit = $this->getParam('limit', 4);

        if (!$iLimit || !$aPage) {
            return false;
        }
        // get pages with the same category
        $aPages = Phpfox::getService('pages')->getSameCategoryPages($aPage['page_id'], $iLimit);

        if (!count($aPages)) {
            return false;
        }

        $this->template()->assign([
            'aPages' => $aPages,
            'sDefaultCoverPath' => Phpfox::getParam('pages.default_cover_photo')
        ]);

        return 'block';
    }

    /**
     * block settings
     * @return array
     */
    public function getSettings()
    {
        return [
            [
                'info' => _p('block_people_also_like_info'),
                'description' => _p('block_people_also_like_description'),
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
}
