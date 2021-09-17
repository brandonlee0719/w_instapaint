<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="js_box_actions">
    <div>
        {_p var="Import File"}
        <span>{_p var="App, Theme, Package, Language, ..."}</span>
		<span>
			<input type="file" name="file" class="btn ajax_upload" data-url="{url link='admincp.app.add' posted='true'}">
		</span>
    </div>
</div>
