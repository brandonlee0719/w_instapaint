<div class="addthis_block">
    {plugin call='share.template_block_addthis_start'}
    <script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid={$sAddThisPubId}"></script>
    {if $sAddThisShareButton}
    {$sAddThisShareButton}
    {else}
    <div class="addthis_toolbox addthis_32x32_style">
        <a class="addthis_button_facebook"></a>
        <a class="addthis_button_twitter"></a>
        <a class="addthis_button_email"></a>
        <a class="addthis_button_linkedin"></a>
        <a class="addthis_button_compact"></a>
    </div>
    {/if}
    {literal}
    <script language="javascript" type="text/javascript">
        $Behavior.onLoadAddthis = function () {
            if (typeof addthis != 'undefined') {
                $('.addthis_block').length > 0 && typeof addthis.layers.refresh === 'function' && addthis.layers.refresh();
                $('.addthis_toolbox').length > 0 && addthis.toolbox('.addthis_toolbox');
            }
        }
    </script>
    {/literal}
    {if ($sAddthisUrl || $sAddthisTitle || $sAddthisDesc || $sAddthisMedia)}
    {literal}
        <script type="text/javascript">
            $Behavior.onUpdateAddthis = function () {
                if (typeof addthis != 'undefined') {
                    addthis.update('share', 'url', "{/literal}{$sAddthisUrl}{literal}");
                    addthis.update('share', 'title', "{/literal}{$sAddthisTitle|clean}{literal}");
                    addthis.update('share', 'description', "{/literal}{$sAddthisDesc|striptag|clean|shorten:200:'...'}{literal}");
                    addthis.update('share', 'media', "{/literal}{$sAddthisMedia}{literal}");
                }
            }
        </script>
    {/literal}
    {/if}
    {plugin call='share.template_block_addthis_end'}
</div>