{literal}
<style>
    .address-card {
        box-shadow: 0 2px 1px 0 rgba(0,0,0,.16);
        line-height: 19px;
    }
</style>
{/literal}

{if $error}
<div class="alert alert-danger" role="alert">{$error}</div>
<a class="btn btn-default" href="/client-dashboard/addresses">Go to My Addresses</a>
{elseif $csrfError}
<div class="alert alert-danger" role="alert"><strong>CSRF detected!</strong> For your security, please logout immediately and report this incident to a developer.</div>
{else}

<p>Are you sure you want to delete this address?</p>

<div class="row">

    <div class="col-md-4">
        <ul class="list-group address-card">
            <li class="list-group-item">
                <strong>{$address.full_name}</strong><br>
                {$address.street_address}<br>
                {if $address.street_address_2}{$address.street_address_2}<br>{/if}
                {$address.city}, {$address.state_province_region} {$address.zip_code}<br>
                {$address.country_name}<br>
                Phone number: {$address.phone_number}<br>
            </li>
        </ul>
    </div>

</div>

<form method="post">
    <div class="form-group">
        <input type="hidden" name="val[token]" class="form-control" required="required" value="{$token}"/>
    </div>
    <div class="form-group">
        <a class="btn btn-default" href="/client-dashboard/addresses">Cancel</a>
        <button type="submit" class="btn btn-danger" name="_submit">Delete</button>
    </div>
</form>
{/if}
