<?php 

 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<div class="page_section_menu page_section_menu_header">
	<div>
		<ul class="nav nav-tabs nav-justified">
			<li {if $bShowSongs} class="active"{/if}>
				<a href="{$sSongLink}">
					{_p var='songs'}
				</a>
			</li>
			<li {if !$bShowSongs} class="active"{/if}>
                <a href="{$sAlbumLink}">
                    {_p var='albums'}
                </a>
			</li>
		</ul>
	</div>
	<div class="clear"></div>
</div>