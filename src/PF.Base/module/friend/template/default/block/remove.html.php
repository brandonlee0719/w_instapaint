<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Friend
 * @version 		$Id: top.html.php 1135 2009-10-05 12:59:10Z Miguel_Espinoza $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if $aUser.user_id != Phpfox::getUserId()}
<div class="pages_view_sub_menu">
    <ul>
        {if isset($aUser.is_friend) && $aUser.is_friend}
        <li>
            <a href="#" onclick="$Core.jsConfirm({left_curly}message: '{_p var='are_you_sure'}'{right_curly}, function(){left_curly} $.ajaxCall('friend.delete', 'friend_user_id={$aUser.user_id}&reload=1');{right_curly}, function(){left_curly}{right_curly}); return false;" class="no_ajax_link">
                {_p var='remove_friend'}
            </a>
        </li>
        {/if}
    </ul>
</div>
{/if}
