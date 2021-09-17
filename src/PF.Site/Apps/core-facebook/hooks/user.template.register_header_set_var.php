<?php
if (setting('m9_facebook_enabled')) {
    Phpfox::getLib('template')->assign('bCustomLogin', true);
}
