<div class="block">
    <div class="title">
        {_p('All Time Statistics')}
    </div>
    <div class="content stats-me">
        {foreach from=$aItems item=aItem}
        <div class="item-separated stat-item clearfix">
            <div class="img pull-left">
                <img src="{img theme=$aItem.photo return_url=1}" width=60 height=60/>
            </div>
            <div class="text-center pull-right" style="width: 100px">
                <div class="text-muted text-center" style="font-size: 110%;">{$aItem.phrase}</div>
                <strong class="value" style="font-size: 200%">{$aItem.value|number_format}</strong>
            </div>
        </div>
        {/foreach}
    </div>
    <div class="bottom">
        <ul>
            <li id="js_block_bottom_1" class="first">
                <a href="{url link='admincp.core.stat'}" " id="js_block_bottom_link_1">
                    {_p var='view_more'}
                </a>
            </li>
        </ul>
    </div>
</div>