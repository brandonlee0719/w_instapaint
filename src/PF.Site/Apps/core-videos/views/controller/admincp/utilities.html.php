<?php
defined('PHPFOX') or exit('NO DICE!');
?>

{if !$isError}
<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">{_p('ffmpeg_version')}</div>
    </div>
    <div class="panel-body">
        <label>{_p('this_will_display_the_current_installed_version_of_ffmpeg')}</label>
        <textarea rows="10" readonly class="form-control">{$sVersion}</textarea>
    </div>
</div>
<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">{_p('supported_video_formats')}</div>
    </div>
    <div class="panel-body">
        <label>{_p('this_will_run_and_show_the_output_of_ffmpeg_formats_please_see_this_page_for_more_info')}</label>
        <textarea rows="10" readonly class="form-control">{$sFormat}</textarea>
    </div>
</div>
{else}
<div class="panel panel-default">
    <div class="panel-body">
        <div class="alert alert-warning">
            {_p('ffmpeg_is_not_something_is_wrongured_or_ffmpeg_path_is_not_correct_please_try_again')}
        </div>
    </div>
</div>
{/if}