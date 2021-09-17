<?php 

defined('PHPFOX') or exit('NO DICE!'); 

?>
{if $sBookmarkDisplay == 'menu' && !empty($sShareModuleId)}
<li class=""><a href="#" onclick="tb_show('{_p var='share' phpfox_squote=true}', $.ajaxBox('share.popup', 'height=300&amp;width=550&amp;type={$sBookmarkType}&amp;url={$sBookmarkUrl}&amp;title={$sBookmarkTitle}{if isset($sFeedShareId) && $sFeedShareId > 0}&amp;feed_id={$sFeedShareId}{/if}{if isset($sFeedType)}&amp;is_feed_view=1{/if}&amp;sharemodule={$sShareModuleId}')); return false;"{if $bIsFirstLink} class="first"{/if}>{$sExtraContent}{_p var='share'}</a></li>
{elseif $sBookmarkDisplay == 'menu_btn' && !empty($sShareModuleId)}
<a href="#" onclick="tb_show('{_p var='share' phpfox_squote=true}', $.ajaxBox('share.popup', 'height=300&amp;width=550&amp;type={$sBookmarkType}&amp;url={$sBookmarkUrl}&amp;title={$sBookmarkTitle}{if isset($sFeedShareId) && $sFeedShareId > 0}&amp;feed_id={$sFeedShareId}{/if}{if isset($sFeedType)}&amp;is_feed_view=1{/if}&amp;sharemodule={$sShareModuleId}')); return false;"{if $bIsFirstLink} class="first"{/if}>
<i class="fa fa-share"></i></a>
{elseif $sBookmarkDisplay == 'menu_link' && !empty($sShareModuleId)}
<li><a href="#" onclick="tb_show('{_p var='share' phpfox_squote=true}', $.ajaxBox('share.popup', 'height=300&amp;width=550&amp;type={$sBookmarkType}&amp;url={$sBookmarkUrl}&amp;title={$sBookmarkTitle}')); return false;"{if $bIsFirstLink} class="first"{/if}>{img theme='icon/share.png' class='item_bar_image'} {_p var='share'}</a></li>
{elseif $sBookmarkDisplay == 'image' && !empty($sShareModuleId)}
<a href="#" onclick="tb_show('{_p var='share' phpfox_squote=true}', $.ajaxBox('share.popup', 'height=300&amp;width=350&amp;type={$sBookmarkType}&amp;url={$sBookmarkUrl}&amp;title={$sBookmarkTitle}')); return false;">{img theme='misc/icn_share.png' class='v_middle'} {_p var='share'}</a>
{elseif !empty($sShareModuleId)}
<a href="#">{img theme='misc/add.png' alt='' style='vertical-align:middle;'}</a> <a href="#" onclick="tb_show('{_p var='share' phpfox_squote=true}', $.ajaxBox('share.popup', 'height=300&amp;width=350&amp;type={$sBookmarkType}&amp;url={$sBookmarkUrl}&amp;title={$sBookmarkTitle}')); return false;">{_p var='share'}</a>
{/if}