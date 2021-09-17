<?php
/**
 * [PHPFOX_HEADER]
 *
 * @copyright        [PHPFOX_COPYRIGHT]
 * @author           Raymond Benc
 * @package          Phpfox
 * @version          $Id: news.html.php 2826 2011-08-11 19:41:03Z Raymond_Benc $
 */

defined('PHPFOX') or exit('NO DICE!');

?>
<div class="block">
    <div class="title">
        {_p var='phpfox_news_and_updates'}
    </div>
    <div class="content">
        {foreach from=$aPhpfoxNews name=news item=aNews}
        <div class="item-separated">
            <a style="font-size: 110%;" href="{$aNews.link}" target="_blank">{$aNews.title|clean}</a>
            <div class="text-muted">
                {$aNews.posted_on}
            </div>
        </div>
        {/foreach}
    </div>
    <div class="bottom">
        <ul>
            <li id="js_block_bottom_1" class="first">
                <a href="https://www.phpfox.com/blog/category/community-roundup/" target="_blank" id="js_block_bottom_link_1">
                    {_p var='view_more'}
                </a>
            </li>
        </ul>
    </div>
</div>