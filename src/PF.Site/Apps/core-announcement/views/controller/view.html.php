<?php 
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="announcement-detail">
    <div class="item-time">
        {$aAnnouncement.posted_on}
    </div>
    <div class="announcement-intro item-intro">
        <i>{$aAnnouncement.intro_var|parse}</i>
    </div>
    <div class="item-desc item_view_content">
        {$aAnnouncement.content_var|parse}
    </div>
</div>