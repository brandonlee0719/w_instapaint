<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 * @copyright       [PHPFOX_COPYRIGHT]
 * @author          phpFox
 * @package         Phpfox_Component
 *
 */
class Friend_Component_Controller_Suggestion extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        Phpfox::isUser(true);

        $this->template()->setTitle(_p('friend_suggestions'))
            ->setBreadCrumb(_p('my_friends'), $this->url()->makeUrl('friend'))
            ->setBreadCrumb(_p('suggestions'), '', true)
            ->assign(array(
                    'aSuggestions' => Phpfox::getService('friend.suggestion')->get()
                )
            );
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('friend.component_controller_suggestion_clean')) ? eval($sPlugin) : false);
    }
}
