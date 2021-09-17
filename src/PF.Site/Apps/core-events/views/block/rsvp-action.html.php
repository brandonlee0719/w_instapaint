<?php
/**
 * [PHPFOX_HEADER]
 *
 */

defined('PHPFOX') or exit('NO DICE!');

?>
<div class="item-choice">
    <a data-toggle="dropdown" class="btn  btn-default btn-icon btn-sm">
        <span class="txt-label">
            {if isset($aEvent.rsvp_id)}
                {if $aEvent.rsvp_id == 1}
                    <i class="ico ico-check"></i>&nbsp;{_p var='attending'}
                {elseif $aEvent.rsvp_id == 2}
                    {_p var='maybe_attending'}
                {elseif $aEvent.rsvp_id == 3}
                    {_p var='not_attending'}
                {else}
                    {_p var='interested_ask'}
                {/if}
            {else}
                {_p var='interested_ask'}
            {/if}
        </span>
        <i class="ico ico-caret-down"></i>
    </a>
    <ul class="dropdown-menu dropdown-menu-checkmark">
        <li role="presentation">
            <a data-event-id="{$aEvent.event_id}" data-toggle="event_rsvp" rel="1" {if isset($aEvent.rsvp_id) && $aEvent.rsvp_id == 1}class="is_active_image"{/if}>
                {_p var='attending'}
            </a>
        </li>
        <li role="presentation">
            <a data-event-id="{$aEvent.event_id}" data-toggle="event_rsvp" rel="2" {if isset($aEvent.rsvp_id) && $aEvent.rsvp_id == 2}class="is_active_image"{/if}>
                {_p var='maybe_attending'}
            </a>
        </li>
        <li role="presentation">
            <a data-event-id="{$aEvent.event_id}" data-toggle="event_rsvp" rel="3" {if isset($aEvent.rsvp_id) && $aEvent.rsvp_id == 3}class="is_active_image"{/if}>
                {_p var='not_attending'}
            </a>
        </li>
    </ul>
</div>