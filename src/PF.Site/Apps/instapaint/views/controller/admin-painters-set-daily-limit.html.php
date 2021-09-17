{literal}
<style>
    .form-group {
        max-width: 500px;
    }
    .form-group-short {
        max-width: 80px;
    }
</style>
{/literal}
{if $painter}
    {if $painter.user_image}
    <img src="/PF.Base/file/pic/user/{$painter.user_image}">
    <br><br>
    {/if}
    <strong>Painter name:</strong> {$painter.full_name}
    <br><br>
<form style="display:inline-block" method="post">
    <label class="control-label" for="daily-limit">Daily jobs limit</label>
    <div class="form-group-short">
        <input id="daily-limit" type="number" min="0" step="1" name="val[daily_limit]" value="{$maxDailyLimit}" class="form-control">
    </div>
    <hr>
    <div class="form-group">
        <a class="btn btn-default" href="/admin-dashboard/painters/approved/">Cancel</a>
            <input type="hidden" name="val[token]" class="form-control" required="required" value="{$token}"/>
            <input type="hidden" name="val[approval_request_id]" class="form-control" required="required" value="{$approvalRequest.approval_request_id}"/>
            <button type="submit" class="btn btn-primary">Update limit</button>
        </form>
    </div>
{else}
<div class="alert alert-danger" role="alert"><i class="fas fa-exclamation-triangle"></i> The painter doesn't exist.</div>
<a class="btn btn-default" href="/admin-dashboard/painters/">Go to painters</a>
{/if}
