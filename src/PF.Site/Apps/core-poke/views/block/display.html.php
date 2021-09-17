<?php
	defined('PHPFOX') or exit('No dice!');
?>

<!--IF AJAX_PAGING-->
{if empty($bIsPaging)}
	<div class="{if !$bIsBlock}js_core_poke_list_items{/if}">
		{/if}
		    {foreach from=$aPokes item=aPoke}
			    <div class="core_poke_item js_core_poke_item_{$aPoke.user_id}" id="poke_{$aPoke.user_id}">
			    	<div class="item-outer">
			    		<div class="item-media">{img user=$aPoke suffix='_50_square' max_width=40 max_height=40 class='v_middle'}
			        		<a href="javascript:void(0)" class="poke-delete" onclick="$.ajaxCall('poke.ignore', 'user_id={$aPoke.user_id}', 'GET');"><i class="ico ico-close"></i></a>
			        	</div>
			        	<div class="item-inner">
			        		{$aPoke|user}	
				        	<a href="javascript:void(0)" class="poke-back js_hover_title" onclick="$.ajaxCall('poke.dopoke', 'user_id={$aPoke.user_id}&amp;type=1', 'GET'); return false;"><i class="ico ico-smile-o"></i><span class="js_hover_info">{_p var='poke'}</span></a>
			        	</div>
			        </div>
			    </div>
		    {/foreach}
		    {if !$bIsBlock}{pager}{/if}
		<!--IF AJAX_PAGING-->
		{if empty($bIsPaging)}
	</div>
{/if}

