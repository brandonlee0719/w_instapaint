{literal}
<style>
    .order {
        border: 1px #ddd solid;
        border-radius: 5px;
        margin-bottom: 15px;
    }
    .order:nth-last-child(2) {
        margin-bottom: 0;
    }
    .order-photo {
        max-width: 100%;
        max-height: 400px;
    }
    .order-header {
        padding: 15px 0;

    }
    .order-body {
        padding: 20px 0;
    }
    .order-header {
        border-bottom: 1px solid lightgrey;
        background-color: #f6f6f6;
        color: #555;
    }
    .order-header > div:last-child {
        text-align: right;
    }
    .order-header-title {
        margin-bottom: 2px;
        text-transform: uppercase;
        font-size: 11px;
    }
    .order-body-title {
        font-size: 16px;
        color: #000;
        text-transform: capitalize;
    }
    .order-body-description {
        margin-bottom: 12px;
    }
    .order-photo-container {
        text-align: center;
    }
    .order-summary-data {
        line-height: 22px;
    }
    .order a:hover {
        text-decoration: underline;
    }
    a.btn:hover {
        text-decoration: none;
    }
     .form-group {
         max-width: 500px;
     }
    .form-group-wide {
        max-width: 100%;
    }
    .form-group-short {
        max-width: 310px;
    }
    .order-summary-data {
        line-height: 22px;
    }
    .order-summary {
        display: none;
    }
    .coupon-message {
        display: none;
    }
    #photo-error-empty {
        display: none;
    }
    #photo-preview {
        display: none;
    }

    #photo-preview img {
        max-width: 380px;
        max-height: 500px;
    }
</style>
{/literal}

{if $csrfError}
<div class="alert alert-danger" role="alert"><strong>CSRF detected!</strong> For your security, please logout immediately and report this incident to a developer.</div>
{/if}

<form method="post">


    <div class="form-group">
        <label class="control-label" for="reason">Cancellation reason</label>
        <textarea id="reason" required="required" type="text" name="val[drop_reason]" class="form-control" placeholder="Required reason for order cancellation"></textarea>
    </div>

    <div class="form-group">
        <input type="hidden" name="val[token]" class="form-control" required="required" value="{$token}"/>
    </div>

    <div class="form-group">
        <a href="/painter-dashboard/orders/" class="btn btn-primary" name="_submit">Go back to my orders</a>
        <button id="submit-button" type="submit" class="btn btn-danger pull-right" name="_submit">Cancel Order</button>
    </div>

</form>
