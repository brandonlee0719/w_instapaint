<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: nofollow.html.php 4165 2012-05-14 10:43:25Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">{_p var='rewrite_url'}</div>
    </div>
    <div class="table-responsive">
        <table class="table table-admin">
            <thead>
                <tr id="tblHeader">
                    <th id="thActions">{_p var='options'}</th>
                    <th>{_p var='this_url'}</th>
                    <th>{_p var='will_show_this_page'}</th>
                </tr>
            </thead>
            <tbody>
                <tr id="trAddNew">
                    <td colspan="3" id="tdAddNew">
                        <a role="button" class="btn btn-link" onclick="$Core.AdminCP.Rewrite.addNew();">{_p var='add_new_rewrite'}</a>
                    </td>
                </tr>
                <tr id="templateEntry">
                    <td>
                        <a class="btn btn-link" title="{_p var='Remove'}" href="javascript:void(0)" onclick="$Core.AdminCP.Rewrite.remove(this);">
                            {_p var='delete'}
                        </a>
                    </td>
                    <td>
                        <input type="text" value="{_p var='original_url'}" class="form-control sOriginal" onblur="$Core.AdminCP.Rewrite.checkOriginal(this)" />
                        <span class="invalidOriginal text-danger">
                            <i class="fa fa-flag"></i>
                        </span>
                    </td>
                    <td>
                        <input type="text" value="{_p var='replacement_url'}" onblur="$Core.AdminCP.Rewrite.checkReplacement(this)" class="form-control sReplacement" />
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="panel-footer">
        <div id="processing">
            {img theme='ajax/small.gif'}
        </div>
        <input type="button" class="btn btn-primary" value="{_p var='save'}" onclick="$Core.AdminCP.Rewrite.save();" />
    </div>
</div>
<script type="text/javascript">
	$Behavior.initRewrites = function()
	{l}
		$Core.AdminCP.Rewrite.init('{$jRewrites}');
	{r};
</script>