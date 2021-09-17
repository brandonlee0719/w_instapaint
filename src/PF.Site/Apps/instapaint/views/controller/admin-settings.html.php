{literal}
<style>
    .form-group {
        max-width: 500px;
    }
    .form-group-short {
        max-width: 130px;
    }
</style>
{/literal}

<form style="display:inline-block" method="post">
    <h3>General</h3>
    <label class="control-label" for="min-expedited-days">Min expedited days since order</label>
    <div class="form-group-short">
        <input id="min-expedited-days" type="number" min="0" step="1" name="val[expedited_min_days]" value="{$expeditedMinDays}" class="form-control" required>
    </div>
    <br>
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
    <h3>Style Prices</h3>
    {foreach from=$styles name=style item=style}

    <img style="max-width: 90px; display: block; margin-bottom: 10px" src="/PF.Site/Apps/instapaint/assets/gallery-thumbnails-200px/{$style.style_id}.jpg">
    <label class="control-label" for="style-price-{$style.style_id}">{$style.name}</label>
    <div class="form-group-short">
        <input id="style-price-{$style.style_id}" type="number" min="0" step="0.01" name="val[style_prices][{$style.style_id}]" value="{$style.price_str}" class="form-control" required style="display: inline; width: 85px;"><span style="font-size: 16px; margin-left: 5px">USD</span>
    </div>
    <hr>
    {/foreach}
    <div class="form-group">
        <a class="btn btn-default" href="/admin-dashboard/painters/">Cancel</a>
        <input type="hidden" name="val[token]" class="form-control" required="required" value="{$token}"/>
        <input type="hidden" name="val[approval_request_id]" class="form-control" required="required" value="{$approvalRequest.approval_request_id}"/>
        <button type="submit" class="btn btn-primary">Save changes</button>
    </div>
</form>
