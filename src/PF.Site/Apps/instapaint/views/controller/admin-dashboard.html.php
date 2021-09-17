{literal}
<style>
    .nav-tabs > li.active > a, .nav-tabs > li.active > a:hover, .nav-tabs > li.active > a:focus {
        color: #555;
    }
</style>

{/literal}

<div id="admin-dashboard-main-page" style="display: none"></div>

<div class="alert alert-info" role="alert"><i class="fas fa-chart-line"></i> This is the Admin Dashboard home page, here you can see general statistics about Instapaint.</div>

{if $countOrdersForApproval}
<a href="/admin-dashboard/verify-orders/">
    <div class="alert alert-warning" role="alert"><i class="far fa-clock"></i> {if $countOrdersForApproval == 1}There is 1 painting waiting for approval! Click here to approve or reject it{else}There are {$countOrdersForApproval} paintings waiting for approval! Click here to approve or reject them{/if}</div>
</a>
{/if}

<div>

    <!-- Nav tabs -->
    <ul class="nav nav-tabs" role="tablist">
        <li role="presentation" class="active"><a href="#users" aria-controls="home" role="tab" data-toggle="tab">Users</a></li>
        <li role="presentation"><a href="#orders" aria-controls="orders" role="tab" data-toggle="tab">Orders</a></li>
        <li role="presentation"><a href="#discounts" aria-controls="discounts" role="tab" data-toggle="tab">Discounts</a></li>
        <li role="presentation"><a href="#packages" aria-controls="packages" role="tab" data-toggle="tab">Packages</a></li>
    </ul>

    <!-- Tab panes -->
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane fade in active" id="users">
            <h2>Users</h2>
            <div class="row">
                <div class="col-md-3">
                    <ul class="list-group">
                        <a href="/user/browse/" class="list-group-item"><strong>Total Users</strong> <span class="pull-right">{$stats.counts.users}</span></a>
                    </ul>
                    <ul class="list-group">
                        <a href="/user/browse/?search%5Bgender%5D=&search%5Bfrom%5D=&search%5Bto%5D=&search%5Bcountry%5D=&null=1&search%5Bcity%5D=&search%5Bzip%5D=&search%5Bkeyword%5D=&search%5Btype%5D=0&custom%5B2%5D=1&search%5Bsort%5D=u.full_name&search%5Bsort_by%5D=ASC&search%5Bsubmit%5D=Search" class="list-group-item"><strong>Clients</strong> <span class="pull-right">{$stats.counts.clients}</span></a>
                    </ul>
                </div>

                <div class="col-md-3">
                    <ul class="list-group">
                        <a href="/admin-dashboard/painters/approved/" class="list-group-item"><strong>Approved Painters</strong> <span class="pull-right">{$stats.counts.approved_painters}</span></a>
                    </ul>
                    <ul class="list-group">
                        <a href="/admin-dashboard/painters/unapproved" class="list-group-item"><strong>Unapproved Painters</strong> <span class="pull-right">{$stats.counts.unapproved_painters}</span></a>
                    </ul>
                </div>

                <div class="col-md-3">
                    {if $stats.counts.painter_approval_requests}
                    <ul class="list-group">
                        <a href="/admin-dashboard/painters/approval-requests/" class="list-group-item list-group-item-danger"><strong>Painter Requests</strong> <span class="pull-right">{$stats.counts.painter_approval_requests}</span></a>
                    </ul>
                    {else}
                    <ul class="list-group">
                        <li class="list-group-item"><strong>Painter Requests</strong> <span class="pull-right">{$stats.counts.painter_approval_requests}</span></li>
                    </ul>
                    {/if}
                    <ul class="list-group">
                        <li class="list-group-item"><strong>Administrators</strong> <span class="pull-right">{$stats.counts.admins}</span></li>
                    </ul>
                </div>

                <div class="col-md-3">
                    <ul class="list-group">
                        <li class="list-group-item"><strong>Online Users</strong> <span class="pull-right">{$stats.counts.online_users}</span></li>
                    </ul>
                </div>
            </div>

            <hr>

            <div class="row">
                <div class="col-md-12">
                    <h2>Users by Country</h2>

                    <div id="users_by_country_map"></div>
                </div>
            </div>

            <hr>

            <div class="row">
                <div class="col-md-6">
                    <h2>Latest Registrations (7 days)</h2>
                    <div id="latest_registrations_7_days"></div>
                </div>

                <div class="col-md-6">
                    <h2>Latest Registrations (28 days)</h2>
                    <div id="latest_registrations_28_days"></div>
                </div>
            </div>
            <br>
        </div>
        <div role="tabpanel" class="tab-pane fade" id="orders">
            <h2>Orders</h2>
            <div class="row">
                <div class="col-md-3">
                    <ul class="list-group">
                        <li class="list-group-item"><strong>Total Orders</strong> <span class="pull-right">{$stats.counts.orders}</span></li>
                    </ul>
                </div>

                <div class="col-md-3">
                    <ul class="list-group">
                        <li class="list-group-item"><strong>Open Orders</strong> <span class="pull-right">{$stats.counts.open_orders}</span></li>
                    </ul>
                </div>

                <div class="col-md-3">
                    <ul class="list-group">
                        <li class="list-group-item"><strong>Shipped Orders</strong> <span class="pull-right">{$stats.counts.completed_orders}</span></li>
                    </ul>
                </div>

                <div class="col-md-3">
                    <ul class="list-group">
                        <li class="list-group-item"><strong>Cancelled Orders</strong> <span class="pull-right">{$stats.counts.cancelled_orders}</span></li>
                    </ul>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3">
                    <ul class="list-group">
                        <li class="list-group-item"><strong>Pending Payment Orders</strong> <span class="pull-right">{$stats.counts.pending_payment_orders}</span></li>
                    </ul>
                </div>
            </div>


            <div class="row" style="display: none;">
                <div class="col-md-6">
                    <h2>Latest Orders (7 days)</h2>
                    <div id="latest_orders_7_days"></div>
                </div>

                <div class="col-md-6">
                    <h2>Latest Orders (28 days)</h2>
                    <div id="latest_orders_28_days"></div>
                </div>
            </div>
        </div>
        <div role="tabpanel" class="tab-pane fade" id="discounts">
            <h2>Discounts</h2>
            <div class="row">
                <div class="col-md-3">
                    <ul class="list-group">
                        <li class="list-group-item"><strong>Total Discounts</strong> <span class="pull-right">{$stats.counts.discounts}</span></li>
                    </ul>
                    <ul class="list-group">
                        <li class="list-group-item"><strong>Active Discounts</strong> <span class="pull-right">{$stats.counts.active_discounts}</span></li>
                    </ul>
                    <ul class="list-group">
                        <li class="list-group-item"><strong>Expired Discounts</strong> <span class="pull-right">{$stats.counts.expired_discounts}</span></li>
                    </ul>
                </div>

                <div class="col-md-3">
                    <ul class="list-group">
                        <li class="list-group-item"><strong>Total Sales</strong> <span class="pull-right">{$stats.counts.sales}</span></li>
                    </ul>
                    <ul class="list-group">
                        <li class="list-group-item"><strong>Total Coupons</strong> <span class="pull-right">{$stats.counts.coupons}</span></li>
                    </ul>
                </div>

                <div class="col-md-3">
                    <ul class="list-group">
                        <li class="list-group-item"><strong>Active Sales</strong> <span class="pull-right">{$stats.counts.active_sales}</span></li>
                    </ul>
                    <ul class="list-group">
                        <li class="list-group-item"><strong>Active Coupons</strong> <span class="pull-right">{$stats.counts.active_coupons}</span></li>
                    </ul>
                </div>

                <div class="col-md-3">
                    <ul class="list-group">
                        <li class="list-group-item"><strong>Expired Sales</strong> <span class="pull-right">{$stats.counts.expired_sales}</span></li>
                    </ul>
                    <ul class="list-group">
                        <li class="list-group-item"><strong>Expired Coupons</strong> <span class="pull-right">{$stats.counts.expired_coupons}</span></li>
                    </ul>
                </div>

            </div>
        </div>
        <div role="tabpanel" class="tab-pane fade" id="packages">
            <h2>Packages</h2>
            <div class="row">
                <div class="col-md-3">
                    <ul class="list-group">
                        <li class="list-group-item"><strong>Frame Sizes</strong> <span class="pull-right">{$stats.counts.frame_sizes}</span></li>
                    </ul>
                </div>

                <div class="col-md-3">
                    <ul class="list-group">
                        <li class="list-group-item"><strong>Frame Types</strong> <span class="pull-right">{$stats.counts.frame_types}</span></li>
                    </ul>
                </div>

                <div class="col-md-3">
                    <ul class="list-group">
                        <li class="list-group-item"><strong>Shipping Types</strong> <span class="pull-right">{$stats.counts.shipping_types}</span></li>
                    </ul>
                </div>

                <div class="col-md-3">
                    <ul class="list-group">
                        <li class="list-group-item"><strong>Packages</strong> <span class="pull-right">{$stats.counts.packages}</span></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

</div>
