{literal}

{/literal}

{if $painterIsApproved}



{literal}
<style>
    .quick-actions-group {
        max-width: 400px;
        margin-bottom: 10px;
    }
</style>
{/literal}

<div id="admin-dashboard-main-page" style="display: none"></div>

<div class="alert alert-info" role="alert"><i class="fas fa-chart-line"></i> Welcome to your dashboard, here you can quickly find information and links related to your Instapaint account in one place.</div>

{if $countAvailableOrders}
<a href="/painter-dashboard/available-orders/">
<div class="alert alert-success" role="alert"><i class="fas fa-paint-brush"></i> {if $countAvailableOrders == 1}There is 1 available order! Click here to take it!{else}There are {$countAvailableOrders} available orders! Click here to take an order!{/if}</div>
</a>
{/if}

{if $countTakenOrders == 0}
<hr>
<div style="text-align: center"><a href="/painter-dashboard/available-orders/" type="button" class="btn btn-primary"><i class="fas fa-paint-brush"></i>&nbsp;Take your first order</a></div>
{/if}
<hr>

<h2>Your Orders{if $countTakenOrders} ({$countTakenOrders}){/if}</h2>
<div class="row">
    <div class="col-md-3">
        {if $countOpenOrders}
        <ul class="list-group">
            <a href="/painter-dashboard/orders/#open-orders-section" class="list-group-item"><strong>Orders you are painting</strong> <span class="pull-right">{$countOpenOrders}</span></a>
        </ul>
        {else}
        <ul class="list-group">
            <li class="list-group-item"><strong>Orders you are painting</strong> <span class="pull-right">0</span></li>
        </ul>
        {/if}
    </div>

    <div class="col-md-3">
        {if $countOrdersSentForApproval}
        <ul class="list-group">
            <a href="/painter-dashboard/orders/#orders-sent-for-approval-section" class="list-group-item"><strong>Sent for approval</strong> <span class="pull-right">{$countOrdersSentForApproval}</span></a>
        </ul>
        {else}
        <ul class="list-group">
            <li class="list-group-item"><strong>Sent for approval</strong> <span class="pull-right">0</span></li>
        </ul>
        {/if}
    </div>

    <div class="col-md-3">
        {if $countOrdersApprovedForShipping}
        <ul class="list-group">
            <a href="/painter-dashboard/orders/#orders-approved-for-shipping-section" class="list-group-item list-group-item-success"><strong>Approved for shipping</strong> <span class="pull-right">{$countOrdersApprovedForShipping}</span></a>
        </ul>
        {else}
        <ul class="list-group">
            <li class="list-group-item"><strong>Approved for shipping</strong> <span class="pull-right">0</span></li>
        </ul>
        {/if}
    </div>

    <div class="col-md-3">
        {if $countShippedOrders}
        <ul class="list-group">
            <a href="/painter-dashboard/orders/#shipped-orders-section" class="list-group-item"><strong>Shipped</strong> <span class="pull-right">{$countShippedOrders}</span></a>
        </ul>
        {else}
        <ul class="list-group">
            <li class="list-group-item"><strong>Shipped</strong> <span class="pull-right">0</span></li>
        </ul>
        {/if}
    </div>
</div>

<hr>

<h2>Quick Actions</h2>

<div class="list-group quick-actions-group">
    <a href="/PF.Site/Apps/instapaint/assets/docs/painters_guide.pdf" target="_blank" class="list-group-item list-group-item-warning"><i class="fas fa-book"></i> Read the Painter's Guide</a>
    <a href="/painter-dashboard/available-orders/" class="list-group-item"><i class="fas fa-paint-brush"></i> Take a new order</a>
    <a href="/painter-dashboard/orders/" class="list-group-item"><i class="fas fa-boxes"></i> View your taken orders</a>
    <a href="/user/setting/" class="list-group-item"><i class="fas fa-cog"></i> Change your account settings</a>
    <a href="/user/profile/" class="list-group-item"><i class="fas fa-user"></i> Edit your profile info</a>
</div>
{else}
    {if $painterRequestedApproval}
        <div class="alert alert-warning" role="alert"><i class="fas fa-hourglass-start"></i> Your approval request will be reviewed by an administrator as soon as possible. Thank you for your patience.</div>
        <h3>Tips to get approved</h3>
        <p>While you wait for your profile to be verified, make sure to check the following points in order to improve your chances of getting approved:</p>
        <ul style="margin-left: 15px; line-height: 2em">
            <li><a href="{$links.profile}"><i class="fas fa-arrow-circle-right"></i> Upload a profile picture and cover</a></li>
            <li><a href="{$links.profileInfo}"><i class="fas fa-arrow-circle-right"></i> Make sure your profile information is up to date</a></li>
            <li><a href="{$links.addWorkSamples}"><i class="fas fa-arrow-circle-right"></i> Upload a few samples of your work</a></li>
        </ul>
        <br>
        <p>If you have completed the steps above, simply allow some time for an administrator to check your profile. You'll be notified once you get approved, otherwise an administrator might contact you for further details and feedback.</p>
    {else}
        <div class="alert alert-info" role="alert"><i class="fas fa-paint-brush"></i> <strong>Welcome to Instapaint!</strong> Congratulations for choosing to become an Instapaint painter and help people transform their photos into amazing oil pantings.</div>
        <h3>Unlock your dashboard</h3>
        <p>In order to ensure the quality of each painting, Instapaint requires every painter's profile to be subjected to a manual verification process.</p>
        <br>
        <p>Before you submit your approval request, check the following points in order to improve your chances of getting approved:</p>
        <ul style="margin-left: 15px; line-height: 2em">
            <li><a href="{$links.profile}"><i class="fas fa-arrow-circle-right"></i> Upload a profile picture and cover</a></li>
            <li><a href="{$links.profileInfo}"><i class="fas fa-arrow-circle-right"></i> Make sure your profile information is up to date</a></li>
            <li><a href="{$links.addWorkSamples}"><i class="fas fa-arrow-circle-right"></i> Upload a few samples of your work</a></li>
        </ul>
        <br>
        <p>After you submit your approval request, an administrator will manually verify your profile. You'll be notified once you get approved, otherwise an administrator might contact you for further details and feedback.</p>

        <form method="post" action="/painter-dashboard/approval-request">
            <div class="form-group">
                <input type="hidden" name="val[token]" class="form-control" required="required" value="{$token}"/>
            </div>
            <div class="form-group">
                <div class="checkbox">
                    <label><input type="checkbox" name="val[share-permission]" checked="checked">I grant Instapaint permission to promote my paintings on social media.</label>
                </div>
            </div>
            <div class="form-group">
                <div class="checkbox">
                    <label><input type="checkbox" required>I have read and accept the&nbsp;<a target="_blank" href="/terms/">terms and conditions.</a></label>
                </div>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary" name="_submit">Request Approval</button>
            </div>
        </form>
    {/if}
{/if}
