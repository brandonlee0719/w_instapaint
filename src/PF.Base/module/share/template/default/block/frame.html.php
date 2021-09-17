<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Share
 * @version 		$Id: frame.html.php 6769 2013-10-11 09:08:02Z Miguel_Espinoza $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if Phpfox::isUser() && $iFeedId > 0}
{module name='feed.share' type=$sBookmarkType url=$sBookmarkUrl}
{else}
{module name='share.friend' type=$sBookmarkType url=$sBookmarkUrl title=$sBookmarkTitle}
{/if}
<script type="text/javascript">$Core.loadInit();</script>