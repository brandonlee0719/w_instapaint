<?php
/**
 * [PHPFOX_HEADER]
 */
namespace Apps\Core_Forums\Controller;

use Phpfox;
use Phpfox_Component;
use Phpfox_Error;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');


class ReadController extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        Phpfox::isUser(true);

        if ($this->request()->getInt('forum')) {
            $aForum = Phpfox::getService('forum')->id($this->request()->getInt('forum'))->getForum();

            if (!isset($aForum['forum_id'])) {
                return Phpfox_Error::display(_p('not_a_valid_forum'));
            }

            if (Phpfox::getService('forum.thread.process')->markRead($aForum['forum_id'])) {
                $this->url()->send('forum', array($aForum['name_url'] . '-' . $aForum['forum_id']),
                    _p('forum_successfully_marked_as_read'));
            }
        } elseif (($sModule = $this->request()->get('module')) && ($iItemId = $this->request()->getInt('item'))) {
            $aCallback = Phpfox::callback($sModule . '.addForum', $iItemId);
            if (isset($aCallback['module'])) {
                if (Phpfox::getService('forum.thread.process')->markRead(0, $aCallback['item'])) {
                    $this->url()->send($aCallback['url_home'], array('forum'), _p('forum_successfully_marked_as_read'));
                }
            }
        } else {
            $aForums = Phpfox::getService('forum')->live()->getForums();
            foreach ($aForums as $aForum) {
                Phpfox::getService('forum.thread.process')->markRead($aForum['forum_id']);

                $aChildrens = Phpfox::getService('forum')->id($aForum['forum_id'])->getChildren();

                if (!is_array($aChildrens)) {
                    continue;
                }

                foreach ($aChildrens as $iForumid) {
                    Phpfox::getService('forum.thread.process')->markRead($iForumid);
                }
            }

            $this->url()->send('forum', null, _p('forum_successfully_marked_as_read'));
        }
        return null;
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('forum.component_controller_read_clean')) ? eval($sPlugin) : false);
    }
}