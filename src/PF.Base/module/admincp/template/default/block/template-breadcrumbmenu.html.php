{if isset($aAdmincpBreadCrumb) && !empty($aAdmincpBreadCrumb)}
{foreach from=$aAdmincpBreadCrumb key=sUrl item=sPhrase}
<a class="child" href="{$sUrl}">{$sPhrase}</a>
{/foreach}
{else if($sSectionTitle)}
<a class="child" href="">{$sSectionTitle}</a>
{/if}