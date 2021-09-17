<?php
namespace Apps\Core_Forums\Service;

use Core_Service_Systems_Category_Process;
use Phpfox;
use Phpfox_Error;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

class Process extends Core_Service_Systems_Category_Process
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('forum');
        $this->_sModule = 'forum';
        parent::__construct();
    }

    /**
     * @param int $iId
     * @param string $sCounter
     * @param bool $bMinus
     * @param int $iTotal
     *
     * @return void
     */
    public function updateCounter($iId, $sCounter, $bMinus = false, $iTotal = 1)
    {
        if ($bMinus && $iTotal == 0) {
            $iTotal = 1;
        }

        $this->database()->update($this->_sTable, array(
            $sCounter => array('= ' . $sCounter . ' ' . ($bMinus ? '-' : '+'), $iTotal)
        ), 'forum_id = ' . (int)$iId
        );

        if (redis()->enabled()) {
            if ($bMinus) {
                redis()->decrby('threads/' . $sCounter . '/' . $iId, $iTotal);
            } else {
                redis()->incrby('threads/' . $sCounter . '/' . $iId, $iTotal);
            }
        }
    }

    /**
     * @param array $aVals
     * @param string $sName
     *
     * @return int
     */
    public function add($aVals, $sName = 'name')
    {
        $this->setCategoryName('forum');
        $sNamePhraseVar = $this->addPhrase($aVals);
        $this->setCategoryName('description');
        $sDescriptionPhraseVar = $this->addPhrase($aVals, 'description', false);

        $iOrder = $this->database()->select('ordering')
            ->from($this->_sTable)
            ->order('forum_id DESC')
            ->execute('getSlaveField');

        $aInsert = array(
            'parent_id' => (empty($aVals['parent_id']) ? 0 : (int)$aVals['parent_id']),
            'is_category' => (isset($aVals['is_category']) ? (int)$aVals['is_category'] : 0),
            'is_closed' => (isset($aVals['is_closed']) ? (int)$aVals['is_closed'] : 0),
            'name' => $sNamePhraseVar,
            'name_url' => '',
            'description' => $sDescriptionPhraseVar,
            'ordering' => ($iOrder + 1)
        );

        $iId = $this->database()->insert($this->_sTable, $aInsert);

        $this->cache()->remove();

        // Plugin call
        if ($sPlugin = Phpfox_Plugin::get('forum.service_process_add__end')) {
            eval($sPlugin);
        }
        return $iId;
    }

    /**
     * @param array $aVals
     * @param string $sName
     *
     * @return bool
     */
    public function update($aVals, $sName = 'name')
    {
        $aForum = $this->database()->select('*')
            ->from($this->_sTable)
            ->where('forum_id = ' . (int)$aVals['edit_id'])
            ->execute('getSlaveRow');

        if ($aForum['parent_id'] > 0) {
            //decrease total thread of new forum
            foreach (Phpfox::getService('forum')->id($aForum['parent_id'])->getParents(true) as $iId) {
                $this->updateCounter($iId, 'total_thread', true, $aForum['total_thread']);
                $this->updateCounter($iId, 'total_post', true, $aForum['total_post']);
            }
        }
        if (!isset($aVals['parent_id'])) {
            $aVals['parent_id'] = 0;
        }
        if (!isset($aVals['edit_id'])) {
            return false;
        }

        if (isset($aVals['name']) && \Core\Lib::phrase()->isPhrase($aVals['name'])) {
            $finalPhrase = $aVals['name'];
            //Update phrase
            $this->updatePhrase($aVals);
        } else {
            $finalPhrase = $this->addPhrase($aVals, 'name');
        }

        if (empty($finalPhrase)) {
            return false;
        }

        if (isset($aVals['description']) && \Core\Lib::phrase()->isPhrase($aVals['description'])) {
            $finalDescriptionPhrase = $aVals['description'];
            //Update phrase
            $this->updatePhrase($aVals, 'description');
        } else {
            $finalDescriptionPhrase = $this->addPhrase($aVals, 'description');
        }

        $aUpdate = array(
            'parent_id' => (empty($aVals['parent_id']) ? 0 : (int)$aVals['parent_id']),
            'is_category' => (isset($aVals['is_category']) ? (int)$aVals['is_category'] : 0),
            'is_closed' => (isset($aVals['is_closed']) ? (int)$aVals['is_closed'] : 0),
            'name' => $finalPhrase,
            'description' => $finalDescriptionPhrase
        );

        $this->database()->update($this->_sTable, $aUpdate, 'forum_id = ' . $aVals['edit_id']
        );

        $this->cache()->remove();

        if ($aVals['parent_id'] > 0) {
            //increase total thread of new forum
            foreach (Phpfox::getService('forum')->id($aVals['parent_id'])->getParents(true) as $iId) {
                $this->updateCounter($iId, 'total_thread', false, $aForum['total_thread']);
                $this->updateCounter($iId, 'total_post', false, $aForum['total_post']);
            }
        }
        return true;
    }

    /**
     * @param int $iId
     * @param array $aVals
     * @return bool
     */
    public function delete($iId, $aVals = [])
    {
        $aForum = $this->database()->select('*')
            ->from($this->_sTable)
            ->where('forum_id = ' . (int)$iId)
            ->execute('getSlaveRow');
        if (isset($aForum['name']) && Phpfox::isPhrase($aForum['name'])) {
            Phpfox::getService('language.phrase.process')->delete($aForum['name'], true);
        }
        if (isset($aForum['description']) && Phpfox::isPhrase($aForum['description'])) {
            Phpfox::getService('language.phrase.process')->delete($aForum['description'], true);
        }

        if (!isset($aForum['forum_id'])) {
            return false;
        }

        $mChildren = Phpfox::getService('forum')->id($aForum['forum_id'])->getChildren();
        $mChildren[] = $aForum['forum_id'];

        if (redis()->enabled()) {
            redis()->del('forum/announcement/' . $aForum['forum_id']);
        }

        if (is_array($mChildren)) {
            foreach ($mChildren as $iChild) {
                $aThreads = $this->database()->select('thread_id')
                    ->from(Phpfox::getT('forum_thread'))
                    ->where('forum_id = ' . $iChild)
                    ->execute('getSlaveRows');

                foreach ($aThreads as $aThread) {
                    $this->database()->delete(Phpfox::getT('forum_thread'), 'thread_id = ' . $aThread['thread_id']);
                    $this->database()->delete(Phpfox::getT('track'),
                        'item_id = ' . $aThread['thread_id'] . ' AND type_id="forum_thread"');
                    $this->database()->delete(Phpfox::getT('forum_announcement'),
                        'thread_id = ' . $aThread['thread_id']);

                    $aPosts = $this->database()->select('post_id')
                        ->from(Phpfox::getT('forum_post'))
                        ->where('thread_id = ' . $aThread['thread_id'])
                        ->execute('getSlaveRows');

                    foreach ($aPosts as $aPost) {
                        $this->database()->delete(Phpfox::getT('forum_post'), 'post_id = ' . $aPost['post_id']);
                        $this->database()->delete(Phpfox::getT('forum_post_text'), 'post_id = ' . $aPost['post_id']);
                    }
                }

                $aMods = $this->database()->select('moderator_id')
                    ->from(Phpfox::getT('forum_moderator'))
                    ->where('forum_id = ' . $iChild)
                    ->execute('getSlaveRows');

                foreach ($aMods as $aMod) {
                    $this->database()->delete(Phpfox::getT('forum_moderator'),
                        'moderator_id = ' . $aMod['moderator_id']);
                    $this->database()->delete(Phpfox::getT('forum_moderator_access'),
                        'moderator_id = ' . $aMod['moderator_id']);
                }
                $aChildForum = $this->database()->select('*')
                    ->from($this->_sTable)
                    ->where('forum_id = ' . (int)$iChild)
                    ->execute('getSlaveRow');
                if (isset($aChildForum['name']) && Phpfox::isPhrase($aChildForum['name'])) {
                    Phpfox::getService('language.phrase.process')->delete($aChildForum['name'], true);
                }
                if (isset($aChildForum['description']) && Phpfox::isPhrase($aChildForum['description'])) {
                    Phpfox::getService('language.phrase.process')->delete($aChildForum['description'], true);
                }
                $this->database()->delete(Phpfox::getT('track'), 'item_id = ' . $iChild . ' AND type_id="forum"');
                $this->database()->delete($this->_sTable, 'forum_id = ' . $iChild);
            }
        }

        $this->cache()->remove();

        return true;
    }

    /**
     * @param $iId
     * @param $aVals
     * @return bool
     */
    public function deleteForum($iId, $aVals)
    {
        $aForum = $this->database()->select('*')
            ->from($this->_sTable)
            ->where('forum_id = ' . (int)$iId)
            ->execute('getSlaveRow');

        if (isset($aForum['name']) && \Core\Lib::phrase()->isPhrase($aForum['name'])) {
            Phpfox::getService('language.phrase.process')->delete($aForum['name'], true);
        }
        if (isset($aForum['description']) && \Core\Lib::phrase()->isPhrase($aForum['description'])) {
            Phpfox::getService('language.phrase.process')->delete($aForum['description'], true);
        }

        if (!isset($aForum['forum_id'])) {
            return false;
        }

        $mChildren = Phpfox::getService('forum')->id($aForum['forum_id'])->getChildren();
        $mChildren[] = $aForum['forum_id'];

        if (redis()->enabled()) {
            redis()->del('forum/announcement/' . $aForum['forum_id']);
        }

        if ($aVals && isset($aVals['delete_type'])) {
            switch ($aVals['delete_type']) {
                case 1:
                    if (is_array($mChildren)) {
                        foreach ($mChildren as $iChild) {
                            $this->deleteForumData($iChild);
                        }
                    }
                    break;
                case 2:
                    if (!empty($aVals['new_forum_id'])) {
                        db()->update($this->_sTable, ['parent_id' => $aVals['new_forum_id']],
                            'parent_id = ' . (int)$aForum['forum_id']);
                        db()->update(':forum_thread', ['forum_id' => $aVals['new_forum_id']],
                            'forum_id = ' . (int)$aForum['forum_id']);
                        db()->update(':forum_announcement', ['forum_id' => $aVals['new_forum_id']],
                            'forum_id = ' . (int)$aForum['forum_id']);
                        //increase total thread of new forum
                        foreach (Phpfox::getService('forum')->id($aVals['new_forum_id'])->getParents() as $iId) {
                            $this->updateCounter($iId, 'total_thread', false, $aForum['total_thread']);
                            $this->updateCounter($iId, 'total_post', false, $aForum['total_post']);
                        }

                        $this->deleteForumData($aForum['forum_id']);
                    }
                    break;
                default:
                    $this->deleteForumData($aForum['forum_id']);
                    break;
            }
        }

        $this->cache()->remove();

        return true;
    }

    public function deleteForumData($iForumId)
    {
        $aForum = $this->database()->select('*')
            ->from($this->_sTable)
            ->where('forum_id = ' . (int)$iForumId)
            ->execute('getSlaveRow');

        $aThreads = $this->database()->select('thread_id')
            ->from(Phpfox::getT('forum_thread'))
            ->where('forum_id = ' . $iForumId)
            ->execute('getSlaveRows');

        foreach ($aThreads as $aThread) {
            $this->database()->delete(Phpfox::getT('forum_thread'), 'thread_id = ' . $aThread['thread_id']);
            $this->database()->delete(Phpfox::getT('track'),
                'item_id = ' . $aThread['thread_id'] . ' AND type_id="forum_thread"');
            $this->database()->delete(Phpfox::getT('forum_announcement'), 'thread_id = ' . $aThread['thread_id']);

            $aPosts = $this->database()->select('post_id')
                ->from(Phpfox::getT('forum_post'))
                ->where('thread_id = ' . $aThread['thread_id'])
                ->execute('getSlaveRows');

            foreach ($aPosts as $aPost) {
                $this->database()->delete(Phpfox::getT('forum_post'), 'post_id = ' . $aPost['post_id']);
                $this->database()->delete(Phpfox::getT('forum_post_text'), 'post_id = ' . $aPost['post_id']);
            }
        }

        $aMods = $this->database()->select('moderator_id')
            ->from(Phpfox::getT('forum_moderator'))
            ->where('forum_id = ' . $iForumId)
            ->execute('getSlaveRows');

        foreach ($aMods as $aMod) {
            $this->database()->delete(Phpfox::getT('forum_moderator'), 'moderator_id = ' . $aMod['moderator_id']);
            $this->database()->delete(Phpfox::getT('forum_moderator_access'),
                'moderator_id = ' . $aMod['moderator_id']);
        }
        $aChildForum = $this->database()->select('*')
            ->from($this->_sTable)
            ->where('forum_id = ' . (int)$iForumId)
            ->execute('getSlaveRow');
        if (isset($aChildForum['name']) && \Core\Lib::phrase()->isPhrase($aChildForum['name'])) {
            Phpfox::getService('language.phrase.process')->delete($aChildForum['name'], true);
        }
        if (isset($aChildForum['description']) && \Core\Lib::phrase()->isPhrase($aChildForum['description'])) {
            Phpfox::getService('language.phrase.process')->delete($aChildForum['description'], true);
        }
        //decrease total thread of new forum
        foreach (Phpfox::getService('forum')->id($aForum['forum_id'])->getParents() as $iId) {
            $this->updateCounter($iId, 'total_thread', true, $aForum['total_thread']);
            $this->updateCounter($iId, 'total_post', true, $aForum['total_post']);
        }
        $this->database()->delete(Phpfox::getT('track'), 'item_id = ' . $iForumId . ' AND type_id="forum"');
        $this->database()->delete($this->_sTable, 'forum_id = ' . $iForumId);
    }

    /**
     * @param int $iForumId
     * @param null|int $iThreadId
     *
     * @return bool
     */
    public function updateLastPost($iForumId, $iThreadId = null)
    {
        // get the last post from this forum
        $aLastPost = $this->database()->select('ft.user_id as thread_user_id, fp.thread_id, fp.time_stamp, fp.update_time,fp.post_id, fp.user_id')
            ->from(Phpfox::getT('forum_thread'), 'ft')
            ->join(Phpfox::getT('forum_post'), 'fp', 'fp.thread_id = ft.thread_id')
            ->where('ft.forum_id = ' . (int)$iForumId)
            ->order('fp.time_stamp DESC')
            ->limit(1)
            ->execute('getSlaveRow');

        // get the parent forum
        $iParentForum = $this->database()->select('parent_id')
            ->from(Phpfox::getT('forum'))
            ->where('forum_id = ' . (int)$iForumId)
            ->execute('getSlaveField');

        // update this forum with the last reply
        $aUpdate = array(
            'thread_id' => 0,
            'post_id' => 0,
            'last_user_id' => 0
        );

        if (isset($aLastPost['post_id']) && $aLastPost['post_id'] > 0) {
            $aUpdate = array(
                'thread_id' => $aLastPost['thread_id'],
                'post_id' => $aLastPost['post_id'],
                'last_user_id' => !empty($aLastPost['last_user_id']) ? $aLastPost['last_user_id'] : $aLastPost['thread_user_id']
            );
        }

        $this->database()->update(Phpfox::getT('forum'), $aUpdate, 'forum_id = ' . (int)$iForumId);

        if ($iThreadId !== null) {
            // by now the last post should already have been deleted
            $aLastPost = $this->database()->select('thread_id, post_id, user_id, time_stamp, update_time')
                ->from(Phpfox::getT('forum_post'))
                ->where('thread_id = ' . (int)$iThreadId)
                ->order('time_stamp DESC')
                ->execute('getSlaveRow');

            $this->database()->update(Phpfox::getT('forum_thread'), array(
                'last_user_id' => $aLastPost['user_id'],
                'post_id' => $aLastPost['post_id']
            ),
                'thread_id = ' . (int)$iThreadId
            );
            $aUpdate = array(
                'thread_id' => $aLastPost['thread_id'],
                'post_id' => $aLastPost['post_id'],
                'last_user_id' => $aLastPost['user_id']
            );
        }
        if ($iParentForum > 0) {
            $this->database()->update(Phpfox::getT('forum'), $aUpdate, 'forum_id = ' . (int)$iForumId);
        }

        return true;
    }

    /**
     * Save access permissions for a specific forum and user group.
     * 1st parameter includes the following post array:
     * - forum_id (INT)
     * - user_group_id (INT)
     *
     * @param array $aVals ARRAY of post values.
     *
     * @return bool TRUE on success, FALSE on failure.
     */
    public function savePerms($aVals)
    {
        $this->database()->delete(Phpfox::getT('forum_access'),
            'forum_id = ' . (int)$aVals['forum_id'] . ' AND user_group_id = ' . $aVals['user_group_id']);
        foreach ($aVals['perm'] as $sVar => $sValue) {
            $this->database()->insert(Phpfox::getT('forum_access'), array(
                    'forum_id' => $aVals['forum_id'],
                    'user_group_id' => $aVals['user_group_id'],
                    'var_name' => $sVar,
                    'var_value' => $sValue
                )
            );
        }
        $this->cache()->remove('forum_group_permission_' . $aVals['user_group_id'] . '_' . $aVals['forum_id']);
        cache()->del('forum/access/' . $aVals['forum_id'] . '/' . $aVals['user_group_id']);

        return true;
    }

    /**
     * Reset access permissions for a specific forum and user group.
     *
     * @param int $iForumId Forum ID#
     * @param int $iUserGroupId User group ID#
     *
     * @return bool TRUE on success, FALSE on failure.
     */
    public function resetPerms($iForumId, $iUserGroupId)
    {
        $this->database()->delete(Phpfox::getT('forum_access'),
            'forum_id = ' . (int)$iForumId . ' AND user_group_id = ' . $iUserGroupId);
        $this->cache()->remove('forum_group_permission_' . $iUserGroupId . '_' . $iForumId);
        cache()->del('forum/access/' . $iForumId . '/' . $iUserGroupId);

        return true;
    }

    /**
     * Update category ordering
     *
     * @param array $aOrders
     *
     * @return bool
     */
    public function updateOrder($aOrders)
    {
        foreach ($aOrders as $iCategoryId => $iOrder) {
            $this->database()->update($this->_sTable, ['ordering' => $iOrder], 'forum_id = ' . (int)$iCategoryId);
        }

        // Remove from cache
        $this->cache()->remove();

        return true;
    }

    /**
     * If a call is made to an unknown method attempt to connect
     * it to a specific plug-in with the same name thus allowing
     * plug-in developers the ability to extend classes.
     *
     * @param string $sMethod is the name of the method
     * @param array $aArguments is the array of arguments of being passed
     *
     * @return null
     */
    public function __call($sMethod, $aArguments)
    {
        /**
         * Check if such a plug-in exists and if it does call it.
         */
        if ($sPlugin = Phpfox_Plugin::get('forum.service_process__call')) {
            eval($sPlugin);
            return null;
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
    }
}