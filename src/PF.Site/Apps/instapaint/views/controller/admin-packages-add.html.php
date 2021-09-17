{if $error}
<div class="alert alert-danger" role="alert">{$error}</div>
<a class="btn btn-default" href="/admin-dashboard/packages">Go to Packages</a>
{elseif $csrfError}
<div class="alert alert-danger" role="alert"><strong>CSRF detected!</strong> For your security, please logout immediately and report this incident to a developer.</div>
{else}
<form method="post">
    <div class="form-group">
        <label class="control-label" for="frame-size">Frame Size</label>
        <select id="frame-size" type="text" name="val[frame_size_id]" class="form-control" required="required" autofocus="autofocus">
            <option value="" selected="selected">Select a frame size</option>
            {foreach from=$frameSizes name=frameSize item=frameSize}
            <option value="{$frameSize.frame_size_id}">{$frameSize.name_phrase} (${$frameSize.price_usd}) - {$frameSize.description_phrase}</option>
            {/foreach}
        </select>
    </div>
    <div class="form-group">
        <label class="control-label" for="frame-type">Frame Type</label>
        <select id="frame-type" type="text" name="val[frame_type_id]" class="form-control" required="required">
            <option value="" selected="selected">Select a frame type</option>
            {foreach from=$frameTypes name=frameType item=frameType}
            <option value="{$frameType.frame_type_id}">{$frameType.name_phrase} (${$frameType.price_usd}) - {$frameType.description_phrase}</option>
            {/foreach}
        </select>
    </div>
    <div class="form-group">
        <label class="control-label" for="shipping-type">Shipping Type</label>
        <select id="shipping-type" type="text" name="val[shipping_type_id]" class="form-control" required="required">
            <option value="" selected="selected">Select a shipping type</option>
            {foreach from=$shippingTypes name=shippingType item=shippingType}
            <option value="{$shippingType.shipping_type_id}">{$shippingType.name_phrase} (${$shippingType.price_usd}) - {$shippingType.description_phrase}</option>
            {/foreach}
        </select>
    </div>
    <div class="form-group">
        <input type="hidden" name="val[token]" class="form-control" required="required" value="{$token}"/>
    </div>
    <div class="form-group">
        <a class="btn btn-default" href="/admin-dashboard/packages">Cancel</a>
        <button type="submit" class="btn btn-primary" name="_submit">Add Package</button>
    </div>
</form>

<script>
    // Autofocus on input
    document.getElementById('frame-size').focus();
</script>
{/if}
