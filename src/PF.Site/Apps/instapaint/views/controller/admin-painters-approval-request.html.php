{if $approvalRequest}
    {if $approvalRequest.user_image}
    <img src="/PF.Base/file/pic/user/{$approvalRequest.user_image}">
    <br><br>
    {/if}
    <strong>Painter name:</strong> {$approvalRequest.user_full_name}
    <br><br>
    <strong>Sign-up date:</strong> {$approvalRequest.approval_request_timestamp|convert_time}
    <br><br>
    <strong>Request date:</strong> {$approvalRequest.approval_request_timestamp|convert_time}
    <br><br>
    {if $approvalRequest.cf_about_me}
    <strong>About painter:</strong> {$approvalRequest.cf_about_me}
    <br><br>
    {/if}
        <a class="btn btn-info" target="_blank" href="/{$approvalRequest.user_name}/">View profile in new window</a>
    <hr>
    <div class="form-group">
        <a class="btn btn-default" href="/admin-dashboard/painters/approval-requests/">Cancel</a>
        <form style="display:inline-block" method="post" action="/admin-dashboard/painters/approval-request/deny/">
            <input type="hidden" name="val[token]" class="form-control" required="required" value="{$token}"/>
            <input type="hidden" name="val[approval_request_id]" class="form-control" required="required" value="{$approvalRequest.approval_request_id}"/>
            <button type="submit" class="btn btn-danger" name="_submit">Deny Request</button>
        </form>
        <form style="display:inline-block" method="post" action="/admin-dashboard/painters/approval-request/approve/">
            <input type="hidden" name="val[token]" class="form-control" required="required" value="{$token}"/>
            <input type="hidden" name="val[approval_request_id]" class="form-control" required="required" value="{$approvalRequest.approval_request_id}"/>
            <button type="submit" class="btn btn-success">Approve Request</button>
        </form>
    </div>
{else}

<div class="alert alert-danger" role="alert"><i class="fas fa-exclamation-triangle"></i> The approval request you are trying to view does not exist.</div>
<a class="btn btn-default" href="/admin-dashboard/painters/approval-requests/">Go to approval requests</a>
{/if}
