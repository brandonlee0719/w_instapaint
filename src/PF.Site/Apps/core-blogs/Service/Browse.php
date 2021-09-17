<?php
namespace Apps\Core_Blogs\Service;

use Phpfox;
use Phpfox_Error;
use Phpfox_Plugin;
use Phpfox_Service;

defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Browse
 * @package Apps\Core_Blogs\Service
 */
class Browse extends Phpfox_Service
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('blog');
    }

    /**
     *
     */
    public function query()
    {
        db()->select('blog_text.text_parsed AS text, ')
            ->join(Phpfox::getT('blog_text'), 'blog_text', 'blog_text.blog_id = blog.blog_id');
    }

    /**
     * @param bool $bIsCount
     * @param bool $bNoQueryFriend
     */
    public function getQueryJoins($bIsCount = false, $bNoQueryFriend = false)
    {
        if (Phpfox::isModule('friend') && Phpfox::getService('friend')->queryJoin($bNoQueryFriend)) {
            db()->join(Phpfox::getT('friend'), 'friends',
                'friends.user_id = blog.user_id AND friends.friend_user_id = ' . Phpfox::getUserId());
        }

        if ($this->request()->get((defined('PHPFOX_IS_PAGES_VIEW') ? 'req4' : 'req2')) == 'tag' || $this->request()->get('tag',
                null)) {
            db()->innerJoin(Phpfox::getT('tag'), 'tag', 'tag.item_id = blog.blog_id AND tag.category_id = \'blog\'');
        }

        if ($this->request()->get((defined('PHPFOX_IS_USER_PROFILE') ? 'req3' : 'req2')) == 'category' || $this->request()->get('category',
                null)) {
            db()
                ->innerJoin(Phpfox::getT('blog_category_data'), 'blog_category_data',
                    'blog_category_data.blog_id = blog.blog_id')
                ->innerJoin(Phpfox::getT('blog_category'), 'blog_category',
                    'blog_category.category_id = blog_category_data.category_id');
        }

        if ($bIsCount) {
            db()->group('blog.blog_id');
        }
    }

    /**
     * If a call is made to an unknown method attempt to connect
     * it to a specific plug-in with the same name thus allowing
     * plug-in developers the ability to extend classes.
     *
     * @param string $sMethod is the name of the method
     * @param array $aArguments is the array of arguments of being passed
     * @return null
     */
    public function __call($sMethod, $aArguments)
    {
        /**
         * Check if such a plug-in exists and if it does call it.
         */
        if ($sPlugin = Phpfox_Plugin::get('blog.service_browse__call')) {
            eval($sPlugin);
            return null;
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);

        return false;
    }

    public function processRows(&$aRows)
    {
        foreach ($aRows as $iKey => &$aRow) {
            if (!empty($aRow['image_path'])) {
                $aRow['image'] = Phpfox::getService('blog')->getImageUrl($aRow['image_path'], $aRow['server_id'],
                    '_500');
            } else {
                list($sDescription, $aImages) = Phpfox::getLib('parse.bbcode')->getAllBBcodeContent($aRow['text'],
                    'img');
                $aRow['text'] = $sDescription;
                $aRow['image'] = empty($aImages) ? '' : str_replace('_view', '', $aImages[0]);
            }

            // Retrieve permission
            Phpfox::getService('blog')->retrievePermission($aRow);
        }
    }
}
