{literal}
<style>
    .quick-actions-group {
        max-width: 400px;
        margin-bottom: 10px;
    }
</style>
{/literal}

<div id="admin-dashboard-main-page" style="display: none"></div>

<div class="alert alert-info" role="alert"><i class="fas fa-chart-line"></i> Welcome to your dashboard, in this page you can quickly find information and links related to your Instapaint account.</div>

{if $total_orders == 0}
<hr>
<div style="text-align: center"><a href="/client-dashboard/orders/add/" type="button" class="btn btn-primary"><i class="fas fa-plus-circle"></i>&nbsp;Create your first order</a></div>
<hr>
{/if}

<h2>Your Orders</h2>
<div class="row">
    <div class="col-md-3">
        {if $total_orders}
        <ul class="list-group">
            <a href="/client-dashboard/orders/" class="list-group-item"><strong>Total Orders</strong> <span class="pull-right">{$total_orders}</span></a>
        </ul>
        {else}
        <ul class="list-group">
            <li class="list-group-item"><strong>Total Orders</strong> <span class="pull-right">0</span></li>
        </ul>
        {/if}
    </div>

    <div class="col-md-3">
        {if $pending_payment_orders}
        <ul class="list-group">
            <a href="/client-dashboard/orders/#pending-payment-orders-section" class="list-group-item list-group-item-danger"><strong>Pending Payments</strong> <span class="pull-right">{$pending_payment_orders}</span></a>
        </ul>
        {else}
        <ul class="list-group">
            <li class="list-group-item"><strong>Pending Payments</strong> <span class="pull-right">0</span></li>
        </ul>
        {/if}
    </div>

    <div class="col-md-3">
        {if $open_orders}
        <ul class="list-group">
            <a href="/client-dashboard/orders/#open-orders-section" class="list-group-item"><strong>Open Orders</strong> <span class="pull-right">{$open_orders}</span></a>
        </ul>
        {else}
        <ul class="list-group">
            <li class="list-group-item"><strong>Open Orders</strong> <span class="pull-right">0</span></li>
        </ul>
        {/if}
    </div>

    <div class="col-md-3">
        {if $completed_orders}
        <ul class="list-group">
            <a href="/client-dashboard/orders/#completed-orders-section" class="list-group-item"><strong>Completed Orders</strong> <span class="pull-right">{$completed_orders}</span></a>
        </ul>
        {else}
        <ul class="list-group">
            <li class="list-group-item"><strong>Completed Orders</strong> <span class="pull-right">0</span></li>
        </ul>
        {/if}
    </div>
</div>

<hr>

<h2>Quick Actions</h2>

<div class="list-group quick-actions-group">
    <a href="/client-dashboard/orders/add/" class="list-group-item"><i class="fas fa-plus-circle"></i> Place a new order</a>
    <a href="/client-dashboard/orders/" class="list-group-item"><i class="fas fa-box"></i> View your orders</a>
    <a href="/client-dashboard/addresses/add/" class="list-group-item"><i class="fas fa-map-marker"></i> Add a new address</a>
    <a href="/user/setting/" class="list-group-item"><i class="fas fa-cog"></i> Change your account settings</a>
    <a href="/user/profile/" class="list-group-item"><i class="fas fa-user"></i> Edit your profile info</a>
</div>
