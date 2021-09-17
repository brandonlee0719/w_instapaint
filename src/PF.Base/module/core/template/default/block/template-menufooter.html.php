<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond_Benc
 * @package 		Phpfox
 * @version 		$Id: template-menufooter.html.php 6413 2013-08-05 09:42:03Z Miguel_Espinoza $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<div class="row footer-holder">
	<div class="col-md-6 col-sm-6 copyright">
        {template file='core.block.template-copyright'}
	</div>
	<div class="col-md-6 col-sm-6">
		<ul class="list-inline footer-menu">
			{foreach from=$aFooterMenu key=iKey item=aMenu name=footer}
			<li{if $phpfox.iteration.footer == 1} class="first"{/if}><a href="{url link=''$aMenu.url''}" class="ajax_link{if $aMenu.url == 'mobile'} no_ajax_link{/if}">{_p var=$aMenu.var_name}</a></li>
			{/foreach}
		</ul>
	</div>
</div>
