<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="more-announcement-block">
    {foreach from=$aAnnouncements item=aAnnouncement}
    <div class="announcement-item">
    	<div class="item-outer">
    		<a href="{url link='announcement.view' id=$aAnnouncement.announcement_id}" class="item-title">{$aAnnouncement.subject_var}</a>
        	<div class="item-desc">"<span>{$aAnnouncement.intro_var|parse}</span>"</div>
    	</div>
    </div>
    {/foreach}
</div>