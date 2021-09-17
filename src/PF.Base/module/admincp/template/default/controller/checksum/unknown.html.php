<?php
defined('PHPFOX') or exit('NO DICE!');
?>
{if isset($check)}
	{if $total}
	<div class="alert alert-danger">{$total} {_p var="unknown file(s)"}</div>
    <div class="table-responsive">
        <table class="table table-admin">
            <tbody>
                {foreach from=$unknown name=files item=file}
                <tr>
                    <td>{$file}</td>
                </tr>
                {/foreach}
            </tbody>
        </table>
    </div>
	{else}
	<div class="alert alert-success">
		{_p var='everything_looks_good'}!
	</div>
	{/if}
{else}
	<div id="checkFiles" data-url="{$url}">
		<i class="fa fa-spin fa-circle-o-notch"></i>
	</div>
	{literal}
	<script>
		var isChecked = false;
		$Ready(function() {
			if (isChecked) {
				return;
			}
			isChecked = true;
			$.ajax({
				url: $('#checkFiles').data('url'),
				contentType: 'application/json',
				success: function(e) {
					$('#checkFiles').html(e.content);
				}
			});
		});
	</script>
	{/literal}
{/if}