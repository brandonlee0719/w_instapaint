<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="table form-inline">
    <div class="form-group">
        <label for="select_category"><strong>{_p var='view_in'}</strong>
            <select id="js_mode" class="form-control" onchange="$Core.Newsletter.toggleMode(this);">
                <option value="html">{_p var='html'}</option>
                <option value="plain">{_p var='plain'}</option>
            </select>
    </div>
</div>
<div>
{if $aNewsletter.text_plain != '' && $aNewsletter.text_html != ''}
    <div id="js_view_newsletter_html" class="js_view_newsletter_content" {if $aNewsletter.mode != 'html'}style="display:none;"{/if}>
        {$aNewsletter.text_html}
    </div>
    <div id="js_view_newsletter_plain" class="js_view_newsletter_content" {if $aNewsletter.mode != 'plain'}style="display:none;"{/if}>
        {$aNewsletter.text_plain}
    </div>
{else}
    {_p var='this_newsletter_is_empty'}
{/if}
</div>

{literal}
<script type="text/javascript">
	$Core.Newsletter = {
		toggleMode : function(obj) {
			var sMode = $(obj).val();
			$('.js_view_newsletter_content').hide();
			$('#js_view_newsletter_'+sMode).show();
		}
	}
</script>
{/literal}
