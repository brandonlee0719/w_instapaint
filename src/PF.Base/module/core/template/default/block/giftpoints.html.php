<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: activity.html.php 3326 2011-10-20 09:12:45Z Miguel_Espinoza $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<div id="div_show_gift_points">
	<script type="text/javascript">
        {literal}
		function doGiftPoints()
        {
            var iAmount = {/literal}{if $iCurrentAvailable == 1}1{else}$('#txt_amount_points').val(){/if}{literal},
                errors = $('#send_gift_point_errors');
            if ($.isNumeric(iAmount) == false || (Math.floor(iAmount) != iAmount)) {
                errors.text(oTranslations['please_enter_only_numbers']).show();
            } else if (iAmount <= 0) {
                errors.text(oTranslations['gift_point_must_be_positive_number']).show();
            } else {
                $.ajaxCall('core.doGiftPoints', 'user_id={/literal}{$iTrgUserId}{literal}&amount=' + iAmount);
            }
        }
        {/literal}
	</script>
	<div class="extra_info">
		{_p var='you_are_about_to_gift_activity_points' full_name=$aUser.full_name current=$iCurrentAvailable}
	</div>
	{if $iCurrentAvailable > 1}
		<div class="p_top_8">
			{if $iCurrentAvailable == 1}
				{_p var='you_only_have_one_point_available' full_name=$aUser.full_name}
				<div class="p_top_8">
					<input type="button" value="{_p var='yes'}" class="button btn-primary" onclick="doGiftPoints();" />
                </div>
			{else}
				{_p var='how_many_points_do_you_want_to_gift_away'}
				<div class="p_top_8">
					<input type="text" id="txt_amount_points" value="" class="form-control" /> <input type="button" value="{_p var='gift_points'}" class="button btn-primary m_top_5" onclick="doGiftPoints();" />
                </div>
                <div class="p_top_8">
                    <div class="alert alert-danger" id="send_gift_point_errors" style="display: none;"></div>
                </div>
			{/if}
		</div>
	{else}
		<div class="extra_info">
			{_p var='unfortunately_you_do_not_have_enough_points_to_gift_away'}
		</div>
	{/if}
</div>