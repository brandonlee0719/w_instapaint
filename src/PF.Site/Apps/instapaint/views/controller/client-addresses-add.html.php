{if $csrfError}
<div class="alert alert-danger" role="alert"><strong>CSRF detected!</strong> For your security, please logout immediately and report this incident to a developer.</div>
{else}
{literal}
<style>
    .form-group {
        max-width: 500px;
    }
</style>
{/literal}
<script src="https://unpkg.com/tippy.js@3/dist/tippy.all.min.js"></script>
<form method="post">

    <div class="form-group">
        <label class="control-label" for="country">Country</label>
        <select id="country" type="text" name="val[country_iso]" class="form-control" required>
            <option value="" selected="selected">Select a country</option>
            {foreach from=$countries name=country item=country}
            <option value="{$country.country_iso}" {if $val.country_iso == $country.country_iso}selected{/if}>{$country.name}</option>
            {/foreach}
        </select>
    </div>

    <div class="form-group">
        <label class="control-label" for="full-name">Full name</label>
        <input id="full-name" type="text" name="val[full_name]" class="form-control"
               maxlength="255"  value="{$val.full_name}" required />
    </div>

    <div class="form-group">
        <label class="control-label" for="street-address">Street address</label>
        <input id="street-address" type="text" name="val[street_address]" class="form-control"
               maxlength="255"  value="{$val.street_address}" placeholder="Street and number, P.O. box, c/o." required />
    </div>

    <div class="form-group">
        <input id="street-address-2" type="text" name="val[street_address_2]" class="form-control"
               maxlength="255"  value="{$val.street_address_2}" placeholder="Apartment, suite, unit, building, floor, etc." />
    </div>

    <div class="form-group">
        <label class="control-label" for="city">City</label>
        <input id="city" type="text" name="val[city]" class="form-control"
               maxlength="255"  value="{$val.city}" required />
    </div>

    <div class="form-group">
        <label class="control-label" for="state">State / Province / Region</label>
        <input id="state" type="text" name="val[state_province_region]" class="form-control"
               maxlength="255"  value="{$val.state_province_region}" required />
    </div>

    <div class="form-group">
        <label class="control-label" for="state">Zip code</label>
        <input id="state" type="text" name="val[zip_code]" class="form-control"
               maxlength="255"  value="{$val.zip_code}" required />
    </div>

    <div class="form-group">
        <label class="control-label" for="phone-number" data-tippy-arrow="true" data-tippy-arrow-type="round" data-tippy="Some shipping carriers such as UPS require a customer's phone number for international shipments so they have a point of contact if there is any issue with the delivery. InstaPaint will not disclose your information to third parties.">Phone number</label>
        <div class="input-group">
            <select  id="dial_code_country" type="text" name="val[dial_code_iso]" class="form-control" required>
                <option value="" selected="selected">Select a country</option>
                {foreach from=$countries name=country item=country}
                <option value="{$country.country_iso}" {if $val.dial_code_iso == $country.country_iso}selected{/if}>{$country.name} ({$country.dial_code})</option>
                {/foreach}
            </select>        
            <input id="phone-number" type="text" name="val[phone_number]" class="form-control"
                maxlength="255"  value="{$val.phone_number}" placeholder="Phone number" required title="test"/>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label" for="security-access-code">Building access code</label> (Optional)
        <input id="security-access-code" type="text" name="val[security_access_code]" class="form-control"
               maxlength="255" value="{$val.security_access_code}" placeholder="For buildings or gated communities"/>
    </div>

    <h3>Additional Address Details</h3>
    <p>Preferences are used to plan your delivery. However, shipments can sometimes arrive early or later than planned.</p>
    <div class="form-group">
        <label class="control-label" for="packages">Weekend delivery</label>
        <p>I can receive packages on</p>
        <div class="checkbox">
            <label>
                <input id="receive-saturday" name="val[can_receive_on_saturday]" type="checkbox" value="true" <?php if (!$_POST['val']) echo 'checked' ?> {if $val.can_receive_on_saturday}checked{/if}>
                Saturday
            </label>
            <label>
                <input id="receive-sunday" name="val[can_receive_on_sunday]" type="checkbox" value="true" <?php if (!$_POST['val']) echo 'checked' ?> {if $val.can_receive_on_sunday}checked{/if}>
                Sunday
            </label>
        </div>
    </div>

    <div class="form-group">
        <input type="hidden" name="val[token]" class="form-control" required="required" value="{$token}"/>
    </div>

    <div class="form-group">
        <hr>
        <button type="submit" class="btn btn-primary" name="_submit">Add address</button>
    </div>

</form>
{/if}
