<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<div id="feed_check_new_count" class="hide">
    <div class="btn-info">
    <div id="feed_check_new_count_link" onclick="loadNewFeeds()">{_p var='you_have_number_updates' number=$iCnt}</div></div>
    <script>$Core.checkNewFeedAfter({$aFeedIds});</script>
</div>