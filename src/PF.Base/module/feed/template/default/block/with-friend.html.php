<?php 
 
defined('PHPFOX') or exit('NO DICE!'); 

?>

<li>
	<a href="#" type="button" id="btn_display_with_friend" class="js_btn_display_with_friend activity_feed_share_this_one_link parent js_hover_title btn btn-lg btn-default dont-unbind-children" onclick="return false;">
		<i class="fa fa-user-plus"></i>
		<span class="js_hover_info">
			{_p var='tag_friends'}
		</span>
	</a>
    <script type="text/javascript">
        $Behavior.prepareTagsInit = function()
        {l}
            {if isset($iFeedId) && $iFeedId}
                $Core.FeedTag.iFeedId = {$aForms.feed_id};
            {/if}
            $Core.FeedTag.init();
        {r}
    </script>
    {add_script key='tag-friends.js' value='module_feed'}
</li>