{literal}
<style>
    .label {
        border-radius: 5px;
        position: relative;
        bottom: 1px;
        font-size: 13px;
        float: right;
        height: 20px;
        font-weight: normal;
    }
    .quick-actions-group {
        max-width: 300px;
        margin-bottom: 0px;
    }
</style>

{/literal}

<div class="alert alert-info" role="alert"><i class="fas fa-paint-brush"></i> In this section you can browse and manage painters.</div>

<div class="list-group quick-actions-group">
    <a href="/admin-dashboard/painters/approval-requests/" class="list-group-item"><i class="fas fa-user-clock"></i> Painter Approval Requests {if $approvalRequestsCount}<span class="label label-danger">{$approvalRequestsCount}</span>{/if}</a>
    <a href="/admin-dashboard/painters/approved/" class="list-group-item"><i class="fas fa-user-check"></i> Approved Painters {if $approvedPaintersCount}<span class="label label-primary">{$approvedPaintersCount}</span>{/if}</a>
    <a href="/admin-dashboard/painters/unapproved" class="list-group-item"><i class="fas fa-user"></i> Unapproved Painters {if $unapprovedPaintersCount}<span class="label label-primary">{$unapprovedPaintersCount}</span>{/if}</a>
    <a href="/admin-dashboard/painters/settings" class="list-group-item"><i class="fas fa-cog"></i> Global Painter Settings</a>
</div>
