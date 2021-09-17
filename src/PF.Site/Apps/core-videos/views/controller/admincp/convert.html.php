<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">{_p var='convert_old_videos'}</div>
    </div>
    <div class="panel-body">
        {if $iConvertedUserId}
        <div class="alert alert-info">{_p('video_are_converting_remaining_videos_now_is')} {$iNumberVideos}</div>
        <div class="well-sm">
            <a class="btn-danger btn" href="{url link='admincp.v.convert', cancel_cron=true}">{_p('cancel_convert_via_job')}</a>
        </div>
        {else}
            {if $iNumberVideos}
            <h2>
                {_p var="you_have_number_old_videos_from_feed_videos", number=$iNumberVideos}
                <br/>
                {_p('do_you_want_to_convert_to_new_videos_system')}
            </h2>
            <div class="well-sm">
                <a class="btn-success btn" href="{url link='admincp.v.convert', convert=true}">{_p('yes_convert_directly')}</a>
                <a class="btn-info btn" href="{url link='admincp.v.convert', convert=true  cron=true}">{_p('yes_convert_via_job')}</a>
            </div>
            {else}
            <div class="alert alert-warning">{_p('there_are_no_old_videos_to_convert')}</div>
            {/if}
        {/if}
    </div>
</div>