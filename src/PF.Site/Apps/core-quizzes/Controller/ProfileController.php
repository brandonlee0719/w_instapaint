<?php
/**
 * [PHPFOX_HEADER]
 */

namespace Apps\Core_Quizzes\Controller;

use Phpfox;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 *
 * @copyright        [PHPFOX_COPYRIGHT]
 * @author        phpFox
 * @package        Quiz
 * @version        4.5.3
 */
class ProfileController extends \Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        (($sPlugin = Phpfox_Plugin::get('quiz.component_controller_profile_process_start')) ? eval($sPlugin) : false);

        $this->setParam('bIsProfile', true);

        Phpfox::getComponent('quiz.index', array('bNoTemplate' => true), 'controller');

        (($sPlugin = Phpfox_Plugin::get('quiz.component_controller_profile_process_end')) ? eval($sPlugin) : false);
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('quiz.component_controller_profile_clean')) ? eval($sPlugin) : false);
    }
}