<?php 
defined('PHPFOX') or exit('NO DICE!');
?>
{if $aUser.dob_setting == '3'}
	<div class="message js_no_feed_to_show">{_p var='there_are_no_new_feeds_to_view_at_this_time'}</div>
{else}
    {if !defined('PHPFOX_IS_PAGES_VIEW')}
        <div class="timeline_holder">    
            <div class="timeline_birth_title">		
                {_p var='born_on_birthday' birthday=$sBirthDisplay}
            </div>

        </div>
    {/if}
{/if}