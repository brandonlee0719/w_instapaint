{literal}
<style>
    .address-card {
        box-shadow: 0 2px 1px 0 rgba(0,0,0,.16);
        line-height: 19px;
    }

    .list-group-item:first-child {
        min-height: 160px;
    }
</style>

{/literal}

<div class="alert alert-info" role="alert"><i class="fas fa-map-marker"></i> In this section you can create, edit, and delete your addresses.</div>
{if $addresses}

<div class="row">
    {foreach from=$addresses name=address item=address}
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
            <li class="list-group-item" data-address-id="{$address.address_id}">
                <a href="/client-dashboard/addresses/edit/{$address.address_id}/">Edit</a>&nbsp; | &nbsp;
                <a href="/client-dashboard/addresses/delete/{$address.address_id}/">Delete</a>{if !$address.is_default}&nbsp; | &nbsp;
                <a onclick="setDefaultAddress({$address.address_id}); return false;"href="" class="set-default-button">Set as Default</a>{/if}
                {if $address.is_default}<span class="label label-success pull-right" style="font-size: 13px">Default Address</span>{/if}
            </li>
        </ul>
    </div>
    {/foreach}
</div>

<form id="delete-form" method="post" action="/client-dashboard/addresses/delete/">
    <input id="delete-address-id" type="hidden" name="val[address_id]" class="form-control" required="required" value=""/>
    <input type="hidden" name="val[token]" class="form-control" required="required" value="{$token}"/>
</form>

<form id="set-default-form" method="post" action="/client-dashboard/addresses/set-default/">
    <input id="set-default-address-id" type="hidden" name="val[address_id]" class="form-control" required="required" value=""/>
    <input type="hidden" name="val[token]" class="form-control" required="required" value="{$token}"/>
</form>

<hr>
<div style="text-align: center"">
    <a type="button" class="btn btn-primary" href="/client-dashboard/orders/add/">Create a new order</a>
</div>

{literal}
<script>
    function setDefaultAddress($addressId) {
        document.getElementById("set-default-address-id").value = $addressId;
        document.getElementById("set-default-form").submit()
    }
</script>
{/literal}

{else}
<a class="btn btn-primary" href="/client-dashboard/addresses/add" role="button">Add new address</a>
{/if}

