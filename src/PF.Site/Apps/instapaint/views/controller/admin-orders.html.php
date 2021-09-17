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

<div class="alert alert-info" role="alert"><i class="fas fa-dolly-flatbed"></i> In this section you can view, approve or reject the orders placed on Instapaint.</div>

<div class="list-group quick-actions-group">
    <a href="/admin-dashboard/verify-orders/" class="list-group-item"><i class="fas fa-edit"></i> Approve/Reject Orders {if $countOrdersForApproval}<span class="label label-danger">{$countOrdersForApproval}</span>{/if}</a>
    <a href="/admin-dashboard/open-orders/" class="list-group-item"><i class="fas fa-box-open"></i> View Open Orders {if $countOpenOrders}<span class="label label-primary">{$countOpenOrders}</span>{/if}</a>
    <a href="/admin-dashboard/shipped-orders/" class="list-group-item"><i class="fas fa-truck"></i> View Shipped Orders {if $countShippedOrders}<span class="label label-primary">{$countShippedOrders}</span>{/if}</a>
</div>
