<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<select class="form-control"
    name="val[{$aArgsCountry.name}]{if $bIsMultiple}[]{/if}"
    id="{$aArgsCountry.name}"
    style="{$aArgsCountry.style}"
    {if $bIsMultiple} multiple="multiple" {/if}
    >
    <option value="">{$aArgsCountry.value_title}</option>

        {foreach from=$aCountries key=sIso item=sCountryName}
            <option class="js_country_option" id="js_country_iso_option_{$sIso}" value="{$sIso}">{$sCountryName}</option>
        {/foreach}

</select>

{if isset($country_iso)}

<script type="text/javascript"> $Behavior.setCountry = function()
{l}
    $("#js_country_iso_option_{$country_iso}").prop("selected", true);
{r}
</script>
{/if}