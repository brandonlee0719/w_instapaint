<?php
defined('PHPFOX') or exit('NO DICE!');
?>

<form method="post" action="{$url}" id="page_editor" class="form">
	<div class="page_editor_content">
		<div class="ace_editor" data-ace-mode="smarty">{$data|clean}</div>
	</div>
	<div class="page_editor_data">
		<div class="clearfix">
			<div class="form-group">
				<label for="title">{_p var='title'}</label>
                <input type="text" name="val[title]" value="" id="title">
			</div>

			<div class="form-group">
				<label for="keywords">{_p var='meta_keywords'}</label>
                <input type="text" name="val[keywords]" id="keywords" value="">
			</div>

			<div class="form-group">
				<label for="description">{_p var='meta_description'}</label>
                <input id="description" type="text" name="val[description]" value="">
			</div>

			<div class="form-group">
				<label for="head">{_p var='custom_header'}</label>
                <textarea id="head" name="val[head]" class="form-control"></textarea>
			</div>
			<div class="form-group">
				<button type="submit" class="btn btn-primary" name="_submit">{_p var='save'}</button>
			</div>
		</div>
	</div>
</form>
{literal}
<script type="text/javascript">
	$Ready(function() {
		if ($('.page_editor_data:not(.built)').length) {
			$('.page_editor_data').addClass('built');

			// var custom = $('#page_editor_meta').html().trim();
			$('input[name="val[title]"]').val(document.title);
			$('input[name="val[keywords]"]').val($('meta[name="keywords"]').attr('content'));
			$('input[name="val[description]"]').val($('meta[name="description"]').attr('content'));
			if (typeof(page_editor_meta) == 'string') {
				$('textarea[name="val[head]"]').val(page_editor_meta.head);
			}
		}

		$('#page_editor').submit(function() {
			var t = $(this);

			$.ajax({
				url: t.attr('action'),
				type: 'POST',
				data: t.serialize() + '&content=' + encodeURIComponent($AceEditor.obj.getSession().getValue()),
				success: function(e) {
					p(e);
				}
			});

			return false;
		});
	});
</script>
{/literal}