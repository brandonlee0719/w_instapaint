<?php
/**
 * [PHPFOX_HEADER]
 */

namespace Apps\Core_Events\Block;

use Phpfox;

defined('PHPFOX') or exit('NO DICE!');

class SuggestionBlock extends \Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        if (defined('PHPFOX_IS_USER_PROFILE')) {
            return false;
        }
        $aEvent = $this->getParam('aEvent');
        if (!$aEvent) {
            return false;
        }
        if (empty($aEvent['categories_id'])) {
            return false;
        }
        $iLimit = $this->getParam('limit', 4);
        if (!(int)$iLimit) {
            return false;
        }
        $aSuggestion = Phpfox::getService('event')->getSuggestionEvents($aEvent, $iLimit);

        if (!is_array($aSuggestion) || !count($aSuggestion)) {
            return false;
        }


        $this->template()->assign(array(
                'sHeader' => _p('suggestion_events_uppercase'),
                'aSuggestion' => $aSuggestion,
            )
        );

        return 'block';
    }

    /**
     * @return array
     */
    public function getSettings()
    {
        return [
            [
                'info' => _p('Suggestion Events Limit'),
                'description' => _p('Define the limit of how many suggest events can be displayed when viewing the event detail. Set 0 will hide this block.'),
                'value' => 4,
                'type' => 'integer',
                'var_name' => 'limit',
            ]
        ];
    }
    /**
     * @return array
     */
    public function getValidation()
    {
        return [
            'limit' => [
                'def' => 'int',
                'min' => 0,
                'title' => _p('"Suggestion Events Limit" must be greater than or equal to 0')
            ],
        ];
    }
    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = \Phpfox_Plugin::get('event.component_block_suggestion_clean')) ? eval($sPlugin) : false);
    }
}