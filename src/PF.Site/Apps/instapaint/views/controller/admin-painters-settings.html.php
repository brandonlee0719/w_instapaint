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

<form style="display:inline-block" method="post">
<label class="control-label" for="daily-limit">Default daily jobs limit</label>
<div class="form-group-short">
    <input id="daily-limit" type="number" min="0" step="1" name="val[daily_limit]" value="{$defaultDailyJobsLimit}" class="form-control" required>
</div>
<div class="form-group" style="margin-top: 10px">
    <label style="font-weight: normal; margin-bottom: 0">
        <input name="val[delete_custom_daily_jobs_limits]" type="checkbox">
        Delete custom limits for all painters
    </label>
</div>
<hr>
<div class="form-group">
    <a class="btn btn-default" href="/admin-dashboard/painters/">Cancel</a>
        <input type="hidden" name="val[token]" class="form-control" required="required" value="{$token}"/>
        <input type="hidden" name="val[approval_request_id]" class="form-control" required="required" value="{$approvalRequest.approval_request_id}"/>
        <button type="submit" class="btn btn-primary">Save changes</button>
</div>
</form>
