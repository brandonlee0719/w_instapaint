<?php
namespace Apps\Core_Blogs\Ajax;

defined('PHPFOX') or exit('NO DICE!');

use Phpfox;
use Phpfox_Ajax;

/**
 * Class Ajax
 * @package Apps\Core_Blogs\Ajax
 */
class Ajax extends Phpfox_Ajax
{
    /**
     * Display blog preview. For preview a  blog before publish
     */
    public function preview()
    {
        Phpfox::getBlock('blog.preview', array('sText' => $this->get('text')));
    }

    /**
     * For getting new blog which show up in block What's New of Core
     */
    public function getNew()
    {
        Phpfox::getBlock('blog.new');

        $this->html('#' . $this->get('id'), $this->getContent(false));
        $this->call('$(\'#' . $this->get('id') . '\').parents(\'.block:first\').find(\'.bottom li a\').attr(\'href\', \'' . Phpfox::getLib('url')->makeUrl('blog') . '\');');
    }

    /**
     * Approve action. Click when on drop-down actions link
     */
    public function approve()
    {
        $iId = (int)$this->get('id');
        if ($iId && Phpfox::getService('blog.process')->approve($iId)) {

            // In case approve from listing
            if ($this->get('inline')) {
                $this->remove('#js_blog_entry_' . $iId);
                $iTotalPending = Phpfox::getService('blog')->getPendingTotal();
                if ($iTotalPending) {
                    $this->call('$("#total_pending").text(' . Phpfox::getService('blog')->getPendingTotal() . ')');
                } else {
                    $this->call('setTimeout(function() {$Core.reloadPage();}, 1800);');
                }
            } // In case approve from detail
            else {
                $this->alert(_p('blog_has_been_approved'), _p('blog_approved'), 300, 100, true);
                return $this->call('setTimeout(function() {$Core.reloadPage();}, 1800);');
            }

            $this->alert(_p('blog_has_been_approved'), _p('blog_approved'), 300, 100, true);
        } else {
            $this->alert(_p('blog_not_found'), _p('blog_not_found'), 300, 100, true);
            $this->call('setTimeout(function() {$Core.reloadPage();}, 1800);');
        }
    }

    public function moderation()
    {
        Phpfox::isUser(true);
        $sMessage = '';
        switch ($this->get('action')) {
            case 'approve':
                Phpfox::getUserParam('blog.can_approve_blogs', true);
                foreach ((array)$this->get('item_moderate') as $iId) {
                    Phpfox::getService('blog.process')->approve($iId);
                }
                $sMessage = _p('blog_s_successfully_approved');
                break;
            case 'feature':
                Phpfox::getUserParam('blog.can_feature_blog', true);
                Phpfox::getService('blog.process')->feature((array)$this->get('item_moderate'), 1);
                $this->updateCount();
                $sMessage = _p('blog_s_successfully_featured');
                break;
            case 'unfeature':
                Phpfox::getUserParam('blog.can_feature_blog', true);
                Phpfox::getService('blog.process')->feature((array)$this->get('item_moderate'), 0);
                $this->updateCount();
                $sMessage = _p('blog_s_successfully_unfeatured');
                break;
            case 'delete':
                foreach ((array)$this->get('item_moderate') as $iId) {
                    if (!Phpfox::getService('blog')->isAdminOfParentItem($iId)) {
                        Phpfox::getUserParam('blog.delete_user_blog', true);
                    }
                    Phpfox::getService('blog.process')->delete($iId);
                }
                $sMessage = _p('blog_s_successfully_deleted');
                break;
        }

        $this->alert($sMessage, _p('moderation'), 300, 150, true);
        $this->hide('.moderation_process');
        $this->call('setTimeout(function() {$Core.reloadPage();}, 1800);');
    }

    /**
     * AdminCP only. Active or deactivate a category
     */
    public function toggleActiveCategory()
    {
        $iCategoryId = $this->get('id');
        $iActive = $this->get('active');
        Phpfox::getService('blog.category.process')->toggleActiveCategory($iCategoryId, $iActive);
    }

    /**
     * Feature a blog
     */
    public function feature()
    {
        Phpfox::isUser(true);
        Phpfox::getUserParam('blog.can_feature_blog', true);

        $iBlogId = $this->get('blog_id');
        $bType = $this->get('type');

        if (Phpfox::getService('blog.process')->feature($iBlogId, $bType)) {
            $this->getTemplateLinkAfterProcess($iBlogId);
            $this->alert(($bType == '1' ? _p('blog_successfully_featured') : _p('blog_successfully_un_featured')), null,
                300, 150, true);
            if ($bType == '1') {
                $this->addClass('#js_blog_entry_' . $this->get('blog_id'), 'row_featured_image');
            } else {
                $this->removeClass('#js_blog_entry_' . $this->get('blog_id'), 'row_featured_image');
            }
        }
    }

    /**
     * Sponsor a blog
     *
     * @return null|string
     */
    public function sponsor()
    {
        Phpfox::getUserParam('blog.can_sponsor_blog', true);
        if (!Phpfox::isModule('ad')) {
            return $this->alert('your_request_is_invalid');
        }

        $iBlogId = $this->get('blog_id');
        $bType = $this->get('type');

        // 0 = remove sponsor; 1 = add sponsor
        if (Phpfox::getService('blog.process')->sponsor($iBlogId, $bType)) {
            $aBlog = $this->getTemplateLinkAfterProcess($iBlogId);
            if ($bType == '1') {
                $sModule = _p('blog');
                Phpfox::getService('ad.process')->addSponsor([
                    'module' => 'blog',
                    'item_id' => $this->get('blog_id'),
                    'name' => _p('default_campaign_custom_name', ['module' => $sModule, 'name' => $aBlog['title']])
                ]);
            } else {
                Phpfox::getService('ad.process')->deleteAdminSponsor('blog', $this->get('blog_id'));
            }

            $this->getTemplateLinkAfterProcess($iBlogId);
            $this->alert($bType == '1' ? _p('blog_successfully_sponsored') : _p('blog_successfully_un_sponsored'), null,
                300, 150, true);

            if ($bType == '1') {
                $this->addClass('#js_blog_entry_' . $this->get('blog_id'), 'row_sponsored_image');
            } else {
                $this->removeClass('#js_blog_entry_' . $this->get('blog_id'), 'row_sponsored_image');
            }

        }

        return true;
    }

    public function getTemplateLinkAfterProcess($iBlogId)
    {
        $aItem = Phpfox::getService('blog')->getBlogForEdit($iBlogId);
        Phpfox::getService('blog')->retrievePermission($aItem);

        $template = Phpfox::getLib('template');
        $template->assign(array('aItem' => $aItem));
        $template->getTemplate('blog.block.link');

        if ($aItem['permission_enable']) {
            $this->html('#js_blog_entry_options_' . $iBlogId, $this->getContent(false));
        } else {
            $this->call('$(\'#js_blog_entry_options_' . $iBlogId . '\').parent().remove();');
        }

        return $aItem;

    }

    public function delete()
    {
        $iBlogId = (int) $this->get('blog_id');
        $bCanDelete = Phpfox::getService('blog.permission')->canDelete($iBlogId);
        if ($iBlogId <= 0 || !$bCanDelete) {
            return $this->alert(_p('unable_to_delete_this_item_due_to_privacy_settings'), null, null, null, true);
        }

        Phpfox::getService('blog.process')->delete($iBlogId);
        if ($this->get('is_detail')) {
            Phpfox::addMessage(_p('blog_successfully_deleted'));
            $sLink = Phpfox::getLib('url')->makeUrl('blog');
            $this->call('window.location.href = \''.$sLink.'\'');
        } else {
            $this->alert(_p('blog_successfully_deleted'));
            return $this->call('setTimeout(function() {window.$Core.reloadPage();}, 1800);');
        }
    }

    /**************************************************************************************************************************/
    /*============================= OLD FUNCTIONS SECTION (SHOULD NOT USE AND SHOULD BE REMOVED) =============================*/
    /* Please note that the following section will be removed in phpFox 4.6. Be carefully when using them
    /**************************************************************************************************************************/

    /**
     * @deprecated from 4.6.0
     */
    public function updateCategory()
    {
        Phpfox::isAdmin(true);
        Phpfox::getService('blog.category')->update($this->get('category_id'), $this->get('quick_edit_input'));

        $this->call('window.location.href = \'' . Phpfox::getLib('url')->makeUrl('admincp.blog') . '\'');
    }

    /**
     * @deprecated from 4.6.0
     */
    public function quickSubmit()
    {
        $sId = $this->get('id');
        $sText = $this->get('sText');

        // get the id from the sId variable
        $iId = preg_replace('/[^0-9]/', '', $sId);

        // Only update if text is not empty
        Phpfox::getService('blog')->updateBlogText($iId, $sText);
        $this->call('window.location.href="' . $this->get('sUrl') . '";');
    }

    /**
     * @deprecated from 4.6.0
     */
    public function categorySubOrdering()
    {
        Phpfox::isAdmin(true);
        $aVals = $this->get('val');
        Phpfox::getService('core.process')->updateOrdering(array(
                'table' => 'blog_category',
                'key' => 'category_id',
                'values' => $aVals['ordering']
            )
        );

        Phpfox::getLib('cache')->remove();
    }

    /**
     * @deprecated from 4.6.0
     */
    public function toggleCategory()
    {
        $iCategoryId = $this->get('id');
        $iActive = $this->get('active');
        Phpfox::getService('blog.process')->toggleCategory($iCategoryId, $iActive);
    }
}
