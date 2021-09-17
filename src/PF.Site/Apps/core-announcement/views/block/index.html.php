<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<div id="js_core_announcement_carousel" class="announcement-slider owl-carousel slide{if count($aAnnouncements) == 1} only-one{/if}">
    <!-- Wrapper for slides -->
        {foreach from=$aAnnouncements key=iKey item=aAnnouncement name=announcement}
        <div class="item js_announcement_item_{$aAnnouncement.announcement_id} {if $phpfox.iteration.announcement === 1} active{/if}">
            <div id="core_announcement_{$aAnnouncement.announcement_id}" class="core-announcement-item alert alert-{$aAnnouncement.style} alert-dismissible fade in">
                <div class="item-media">
                    <span style="background-image: url({$aAnnouncement.icon_image return_url=true})"></span>
                </div>
                <div class="item-inner">
                    {if $aAnnouncement.can_be_closed == 1 && Phpfox::getUserParam('announcement.can_close_announcement')}
                    <div class="js_announcement_close">
                        <a href="javascript:void(0)" class="item-delete" onclick="$Core.Announcement.deleteCarouselItem({$aAnnouncement.announcement_id}); return false;"></a>
                    </div>
                    {/if}
                    <a href="{url link='announcement.view' id=$aAnnouncement.announcement_id}" class="js_announcement_subject item-title" data-toggle="tooltip" data-placement="top" title="{$aAnnouncement.subject_var}">
                        {$aAnnouncement.subject_var}
                    </a>
                    <div class="announcement_date item-time">
                        {$aAnnouncement.time_stamp|date}
                    </div>
                    <div class="js_announcement_content item-desc">{$aAnnouncement.intro_var|parse:false}</div>
                    {if !empty($aAnnouncement.content_var) && !isset($bHideViewMore)}
                    <div class="js_announcement_more item-readmore">
                        <a href="{url link='announcement.view' id=$aAnnouncement.announcement_id}">{_p var='read_more'}</a>
                    </div>
                    {/if}
                </div>
            </div>
        </div>
        {/foreach}
</div>

