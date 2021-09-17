{if $csrfError}
<div class="alert alert-danger" role="alert"><strong>CSRF detected!</strong> For your security, please logout immediately and report this incident to a developer.</div>
{elseif $error}

<div class="alert alert-danger" role="alert"><i class="fas fa-exclamation-triangle"></i> The approval request you are trying to approve does not exist.</div>
<a class="btn btn-default" href="/admin-dashboard/painters/approval-requests/">Go to approval requests</a>
{/if}
