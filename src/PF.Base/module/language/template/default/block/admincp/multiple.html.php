<div class="form-group">
    {assign var='value_name' value=$sField"_"$aDefaultLanguage.language_id }
    <label for="{$sField}_{$aDefaultLanguage.language_id}">{if $bRequired}{required}{/if}{_p var=$sLabel} in {$aDefaultLanguage.title}</label>
    {if $sType=='textarea'}
    <textarea {if $bRequired}required{/if} class="form-control" id="{$value_name}" name="{$sFormat}{$aDefaultLanguage.language_id}]" rows="{$sRows}" maxlength={$sMaxLength}>{$sDefaultTranslatedPhraseValue}</textarea>
    {else}
    <input id="{$value_name}" {if $bRequired}required{/if} class="form-control" type="text" name="{$sFormat}{$aDefaultLanguage.language_id}]" value="{$sDefaultTranslatedPhraseValue}" size="{$sSize}" maxlength={$sMaxLength} />
    {/if}
    {help var='admincp.blog_category_add_name'}
    {if count($aOtherLanguages) > 0}
    <p class="help-block"></p>
    <div class="clearfix collapse-placeholder">
        <a role="button" data-cmd="core.toggle_placeholder">{_p var='label_in_other_languages' label=$sLabel}</a>
        <div class="inner">
            <p class="help-block">{_p var=$sHelpPhrase}</p>
            {foreach from=$aOtherLanguages item=aLanguage}
            {assign var='value_name' value=$sField"_"$aLanguage.language_id}
            <div class="form-group">
                <label for="{$value_name}"><strong>{$aLanguage.title}</strong>:</label>
                {if $sType=='textarea'}
                <textarea class="form-control" id="{$value_name}" name="{$sFormat}{$aLanguage.language_id}]" rows="{$sRows}" maxlength={$sMaxLength}><?= $this->_aVars['aTranslatedPhraseValues'][$this->_aVars['aLanguage']['language_id']] ?></textarea>
                {else}
                <input class="form-control" type="text" id="{$value_name}" name="{$sFormat}{$aLanguage.language_id}]" value="<?= $this->_aVars['aTranslatedPhraseValues'][$this->_aVars['aLanguage']['language_id']] ?>" size="{$sSize}" maxlength={$sMaxLength} />
                {/if}
            </div>
            {/foreach}
        </div>
    </div>
    {/if}
</div>

