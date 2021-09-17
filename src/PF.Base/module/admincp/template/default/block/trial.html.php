{if ($is_trial_mode)}
<div class="block">
    <div class="title"{if ($expires <= 2)} style="background:red; color:#fff;" {/if}>
    {_p('phpFox Trial')}
    <a href="https://www.phpfox.com/" target="_blank" class="purchase_trial">{_p var='purchased'}</a>
</div>
<div class="content">
    <div class="info">
        <div class="info_left">
            {_p('Expires')}:
        </div>
        <div class="info_right">
            {if $expires == 0}
            {_p var='today'}
            {else}
            {$expires} {if ($expires == '1')}{_p var='day'}{else}{_p var='days'}{/if}
            {/if}
        </div>
    </div>
</div>
</div>
{/if}