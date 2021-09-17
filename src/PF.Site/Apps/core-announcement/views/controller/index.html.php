<?php 
defined('PHPFOX') or exit('NO DICE!');
?>
<div id="announcements_holder" class="announcement-listing">
	{if is_array($aAnnouncements) && empty($aAnnouncements)}
	<div class="alert alert-danger">
		{_p var='that_announcement_cannot_be_found'}
	</div>
	{elseif $aAnnouncements === false}
	<div class="alert alert-danger">
		{_p var='no_announcements_have_been_added'}
	</div>
	{else}
		{foreach from=$aAnnouncements item=aAnnouncement name=announcement}
			<div class="js_announcement_{$aAnnouncement.announcement_id} article announcement-item">
                {if !empty($aAnnouncement.subject_var) && !isset($aAnnouncement.is_specific)}
                <div class="item-icon">
                    <span class="ico {$aAnnouncement.icon_font}"></span>
                </div>
                <div class="item-outer">
                {/if}
                    <div class="js_announcement_{$aAnnouncement.announcement_id}_subject item-title">
                        {if !empty($aAnnouncement.subject_var) && !isset($aAnnouncement.is_specific)}
                            <a href="{url link='announcement.view' id=$aAnnouncement.announcement_id}">
                                {_p var=$aAnnouncement.subject_var}
                            </a>
                        {/if}
                    </div>
                     <div class="announcement_{$aAnnouncement.announcement_id}_date item-time">
                        {$aAnnouncement.posted_on}
                    </div>
                    <div class="js_announcement_{$aAnnouncement.announcement_id}_content item-desc">
                            "<span>{$aAnnouncement.intro_var|parse}</span>"
                    </div>
                {if !empty($aAnnouncement.subject_var) && !isset($aAnnouncement.is_specific)}
                </div>
                {/if}  
			</div>
		{/foreach}
	{/if}
</div>