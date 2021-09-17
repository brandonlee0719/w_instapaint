<?php
namespace Apps\Core_Blogs\Service;

use Phpfox;

/**
 * Class Permission
 * @package Apps\Core_Blogs\Service
 */
class Permission extends \Phpfox_Service
{
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('blog');
    }

    public function canPublish($aBlog)
    {
        if (!is_array($aBlog) && (int)$aBlog > 0) {
            $aBlog = Phpfox::getService('blog')->getBlogForEdit((int)$aBlog);
        }

        if ($aBlog['blog_id'] && Phpfox::getUserId() == $aBlog['user_id'] && $aBlog['post_status'] == BLOG_STATUS_DRAFT) {
            return true;
        }

        return false;
    }

    /**
     * Must is owner or have permission on all blogs
     * @param $aBlog | can int or object blog
     * @return bool
     */
    public function canEdit($aBlog)
    {
        if (!is_array($aBlog) && (int)$aBlog > 0) {
            $aBlog = Phpfox::getService('blog')->getBlogForEdit((int)$aBlog);
        }

        if ($aBlog['blog_id'] && (Phpfox::getUserParam('blog.edit_user_blog') || Phpfox::getUserParam('blog.edit_own_blog') && Phpfox::getUserId() == $aBlog['user_id'])) {
            return true;
        }

        return false;
    }

    public function canFeature()
    {
        return Phpfox::getUserParam('blog.can_feature_blog');
    }

    /**
     * Must is owner or have permission on all blogs
     * @param $aBlog | can int or object blog
     * @return bool
     */
    public function canDelete($aBlog)
    {
        if (!is_array($aBlog) && (int)$aBlog > 0) {
            $aBlog = Phpfox::getService('blog')->getBlogForEdit((int)$aBlog);
        }

        $bPass = false;
        if ($aBlog['blog_id']) {
            // Check if is owner of groups / pages
            if ($aBlog['module_id'] == 'pages' && Phpfox::isModule('pages') && Phpfox::getService('pages')->isAdmin($aBlog['item_id'])) {
                $bPass = true; // is owner of page
            } elseif ($aBlog['module_id'] == 'groups' && Phpfox::isModule('groups') && Phpfox::getService('groups')->isAdmin($aBlog['item_id'])) {
                $bPass = true; // is owner of page
            }

            // Check if has permission on item
            if ((Phpfox::getUserParam('blog.delete_own_blog') && Phpfox::getUserId() == $aBlog['user_id']) || Phpfox::getUserParam('blog.delete_user_blog')) {
                $bPass = true; // is owner of item or have permission on all
            }
        }

        return $bPass;
    }

    /**
     * Must is owner or have permission on all blogs
     * @param $aBlog | can int or object blog
     * @return bool
     */
    public function canSponsorInFeed($aBlog)
    {
        if (!is_array($aBlog) && (int)$aBlog > 0) {
            $aBlog = Phpfox::getService('blog')->getBlogForEdit((int)$aBlog);
        }

        if (Phpfox::isModule('ad') && $aBlog['user_id'] == Phpfox::getUserId() && Phpfox::isModule('feed') && (Phpfox::getUserParam('feed.can_purchase_sponsor') || Phpfox::getUserParam('feed.can_sponsor_feed')) && ($iSponsorId = Phpfox::getService('feed')->canSponsoredInFeed('blog',
                $aBlog['blog_id']))) {
            return true;
        }

        return false;
    }

    /**
     * Must is owner or have permission on all blogs
     * @param $aBlog | can int or object blog
     * @return bool
     */
    public function canApprove($aBlog)
    {
        if (!is_array($aBlog) && (int)$aBlog > 0) {
            $aBlog = Phpfox::getService('blog')->getBlogForEdit((int)$aBlog);
        }

        if ($aBlog['blog_id'] && ($aBlog['is_approved'] != 1 && Phpfox::getUserParam('blog.can_approve_blogs'))) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function canSponsor()
    {
        if (Phpfox::isModule('ad') && Phpfox::getUserParam('blog.can_sponsor_blog')) {
            return true;
        }

        return false;
    }

    /**
     * Must is owner or have permission on all blogs
     * @param $aBlog | can int or object blog
     * @return bool
     */
    public function canPurchaseSponsor($aBlog)
    {
        if (!is_array($aBlog) && (int)$aBlog > 0) {
            $aBlog = Phpfox::getService('blog')->getBlogForEdit((int)$aBlog);
        }

        if (Phpfox::isModule('ad') && $aBlog['is_sponsor'] != 1 && $aBlog['user_id'] == Phpfox::getUserId() && Phpfox::getUserParam('blog.can_purchase_sponsor')) {
            return true;
        }

        return false;
    }
}
