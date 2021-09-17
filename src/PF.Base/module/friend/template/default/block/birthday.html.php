<?php
/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');
?>
{if is_array($aBirthdays)}
    {foreach from=$aBirthdays key=sDaysLeft item=aBirthDatas name=birthdays}
        <div class="block_event_title_holder">
            <div class="block_event_title">
                {if $sDaysLeft == 1}
                    {_p var='tomorrow'}
                {elseif $sDaysLeft == 2}
                    {_p var='after_tomorrow'}
                {elseif $sDaysLeft < 1}
                    {_p var='today_normal'}
                {else}
                    {_p var='days_left_days' days_left=$sDaysLeft}
                {/if}
            </div>
            {foreach from=$aBirthDatas item=aBirthday name=userbirthdays}
            <div class="user-birthday-item">
                <div class="item-outer">
                    <div class="item-media">
                    {img user=$aBirthday suffix='_50_square'}
                    </div>
                    <div class="item-inner">
                        {$aBirthday|user}
                        {if $aBirthday.show_age}
                            <span class="item-info">{_p var='years_years_old' years=$aBirthday.new_age}</span>
                        {/if}
                    </div>
                </div>
            </div>
            {/foreach}
        </div>
    {/foreach}
{/if}

{if empty($aBirthdays)}
	<div class="alert alert-info">
		{_p var='no_birthdays_coming_up'}
	</div>
{/if}