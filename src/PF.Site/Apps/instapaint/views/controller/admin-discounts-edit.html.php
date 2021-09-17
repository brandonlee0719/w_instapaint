{if $csrfError}
<div class="alert alert-danger" role="alert"><strong>CSRF detected!</strong> For your security, please logout immediately and report this incident to a developer.</div>
{elseif $error}
<div class="alert alert-danger" role="alert">{$error}</div>
{else}
{literal}
<style>
    input.form-control {
        max-width: 300px;
    }
</style>
{/literal}
<form method="post">

    <div class="form-group">
        <label class="control-label" for="name">Name</label>
        <input id="name" type="text" name="val[name]" class="form-control"
               maxlength="255" required="required" autofocus="autofocus" placeholder="Choose a name to identify this discount" value="{$val.name}" />
    </div>

    <div class="form-group">
        <label class="control-label" for="packages">Packages</label>
        <div class="checkbox">
            <label>
                <input id="is-global-discount" name="val[is_global_discount]" type="checkbox" value="true" {if $val.is_global_discount}checked{/if} >
                Apply discount to all packages
            </label>
        </div>

        {foreach from=$packages name=package item=package}
        <div class="checkbox packages-checkboxes" style="display: none">
            <label>
                <input name="val[packages][]" type="checkbox" value="{$package.package_id}" {if $package.package_id|in_array:$val.packages}checked{/if}>
                frame size( {$package.frame_size_name} ) -
                frame type( {$package.frame_type_name} ) -
                shipping( {$package.shipping_type_name} ) -
                total price( ${$package.total_price} )
            </label>
        </div>
        {/foreach}
    </div>

    <div class="form-group">
        <label class="control-label" for="price">Coupon code</label>
        <input id="price" name="val[code]" class="form-control" placeholder="Leave blank if discount is a sale" value="{$val.code}" />
    </div>

    <div class="form-group">
        <label class="control-label" for="amount">Amount</label>
        <div class="input-group">
            <div class="input-group-addon">%</div>
            <input id="amount" type="number" name="val[amount]" class="form-control" step="1" min="0" max="100" required="required" placeholder="The percentage of this discount" value="{$val.amount}"/>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label" for="expiration-date">Expiration date</label>
        <input id="expiration-date" type="date" name="val[expiration]" class="form-control" min="<?php echo /* tomorrow's timestamp */ date('Y-m-d', time() + 86400); ?>" value="{$val.expiration}"/>
    </div>

    <div class="form-group">
        <input type="hidden" name="val[token]" class="form-control" required="required" value="{$token}"/>
    </div>

    <div class="form-group">
        <hr>
        <a class="btn btn-default" href="/admin-dashboard/discounts">Cancel</a>
        <button type="submit" class="btn btn-primary" name="_submit">Save</button>
        <div class="dropup pull-right">
            <button id="delete-button" class="btn btn-danger dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Delete
            </button>
            <ul class="dropdown-menu" aria-labelledby="dropdownMenu2">
                <li><a href="#" onclick="$('#delete-button').addClass('disabled'); $('#delete-form').submit(); return false;">Confirm deletion</a></li>
            </ul>
        </div>
    </div>

</form>

<form id="delete-form" method="post" action="/admin-dashboard/discounts/delete/">
    <input type="hidden" name="val[token]" class="form-control" required="required" value="{$token}"/>
    <input type="hidden" name="val[discount_id]" class="form-control" required="required" value="{$val.discount_id}"/>
</form>
{literal}
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

<script>
    // Autofocus on input
    document.getElementById('name').focus();

    $(document).ready(function () {
        togglePackages();
        $('#is-global-discount').change(function () {
            togglePackages();
        });
    });

    function togglePackages() {
        var isGlobalDiscount = $('#is-global-discount').is(':checked');

        if (!isGlobalDiscount) {
            $('.packages-checkboxes').css('display', 'block');
        } else {
            $('.packages-checkboxes').css('display', 'none');
        }
    }
</script>
{/literal}
{/if}