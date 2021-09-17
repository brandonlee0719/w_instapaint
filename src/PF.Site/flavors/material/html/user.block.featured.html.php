<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Miguel_Espinoza
 * @package 		Phpfox
 * @version 		$Id: featured.html.php 3785 2011-12-14 06:09:36Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<div class="sticky-label-icon sticky-featured-icon">
	<span class="flag-style-arrow"></span>
	<i class="ico ico-diamond"></i>
</div>
<ul class="member-listing featured-members">
{foreach from=$aFeaturedUsers item=aUser name=featured}
	<li class="item-listing">
		<div class="item-outer">
			<div class="item-media">
				{img user=$aUser suffix='_50_square' max_width=50 max_height=50}
			</div>
			
			<div class="item-inner">
				{$aUser|user:'':'':'':12:true}

				<div class="friend-info">
                    {module name='user.friendship' friend_user_id=$aUser.user_id extra_info=true no_button=true mutual_list=true}
                    {module name='user.info' friend_user_id=$aUser.user_id number_of_info=1}
                </div>
			</div>
		</div>
	</li>
{/foreach}
</ul>