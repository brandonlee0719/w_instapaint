<?php 
/**
 * [PHPFOX_HEADER]
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<form class="form" id="event_rsvp" method="post" action="{url link='current'}" data-event-id="{$aEvent.event_id}">
{if isset($aCallback) && $aCallback !== false}
	<div><input type="hidden" name="module" value="{$aCallback.module}" /></div>
	<div><input type="hidden" name="item" value="{$aCallback.item}" /></div>
{/if}
	<div class="item-event-option attending {if $aEvent.rsvp_id == 1} active{/if}">
		<label class="item-event-radio">
			<input type="radio" name="rsvp" value="1" class="v_middle js_event_rsvp" {if $aEvent.rsvp_id == 1}checked="checked" {/if}/> 
			<span class="btn btn-sm btn-default btn-icon js_rsvp_title">{if $aEvent.rsvp_id == 1}<span class="ico ico-check js_checked"></span>{/if} {_p var='attending'}</span>
		</label>
	</div>
	<div class="item-event-option maybe_attending {if $aEvent.rsvp_id == 2} active{/if}">
		<label class="item-event-radio">
			<input type="radio" name="rsvp" value="2" class="v_middle js_event_rsvp" {if $aEvent.rsvp_id == 2}checked="checked" {/if}/> 
			<span class="btn btn-sm btn-default btn-icon js_rsvp_title">{if $aEvent.rsvp_id == 2}<span class="ico ico-check js_checked"></span>{/if} {_p var='maybe_attending'}</span>
		</label>
	</div>
	<div class="item-event-option not_attending {if $aEvent.rsvp_id == 3} active{/if}">
		<label class="item-event-radio">
			<input type="radio" name="rsvp" value="3" class="v_middle js_event_rsvp" {if $aEvent.rsvp_id == 3}checked="checked" {/if}/> 
			<span class="btn btn-sm btn-default btn-icon js_rsvp_title">{if $aEvent.rsvp_id == 3}<span class="ico ico-check js_checked"></span>{/if} {_p var='not_attending'}</span>
		</label>
	</div>
</form>
<div class="item-statistic">
    <span class="item-count-view">{$aEvent.total_view|short_number}</span>{if $aEvent.total_view == 1}{_p var='view__l'}{else}{_p var='views_lowercase'}{/if}
</div>
{literal}
<script>
	$Ready(function() {
		$('#event_rsvp .item-event-option').click(function() {
			var t = $(this), f = $(this).parents('form:first'), rsvp = f.find('input[name="rsvp"]:checked').val();
			$('.item-event-option.active').removeClass('active');
			f.find('.js_checked').remove();
			t.find('.js_rsvp_title').prepend('<span class="ico ico-check js_checked"></span> ');
            t.addClass('active');
			t.find('input').prop('checked', true);
			f.ajaxCall('event.addRsvp', '&id=' + f.data('event-id'));
		});
	});
</script>
{/literal}