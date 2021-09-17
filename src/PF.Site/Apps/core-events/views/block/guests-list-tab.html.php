<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="page_section_menu page_section_menu_header">
<ul class="nav nav-tabs nav-justified">
    <li class="active"><a data-toggle="tab" href="#js_event_attending_attending" rel="js_event_attending_attending">{_p var='attending'} ({$iAttendingCnt})</a></li>
    <li><a data-toggle="tab" href="#js_event_attending_maybe" rel="js_event_attending_maybe">{_p var='maybe_attending'} ({$iMaybeCnt})</a></li>
    <li><a data-toggle="tab" href="#js_event_attending_awaiting" rel="js_event_attending_awaiting">{_p var='awaiting_reply'} ({$iAwaitingCnt})</a></li>
</ul>
</div>
<div class="tab-content">
    <div id="js_event_attending_attending" class="page_section_menu_holder js_event_attending_attending">
        {module name='event.attending' tab='attending' container='.js_event_attending_attending' iEventId=$iEventId}
    </div>
    <div id="js_event_attending_maybe" class="page_section_menu_holder js_event_attending_maybe" style="display: none;">
        {module name='event.attending' tab='maybe' container='.js_event_attending_maybe' iEventId=$iEventId}
    </div>
    <div id="js_event_attending_awaiting" class="page_section_menu_holder js_event_attending_awaiting" style="display: none;">
        {module name='event.attending' tab='awaiting' container='.js_event_attending_awaiting' iEventId=$iEventId}
    </div>
</div>
