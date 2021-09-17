{if $csrfError}
<div class="alert alert-danger" role="alert"><strong>CSRF detected!</strong> For your security, please logout immediately and report this incident to a developer.</div>
{else}
<form method="post">
    <div class="form-group">
        <label class="control-label" for="name">Name</label>
        <input id="name" type="text" name="val[name]" class="form-control"
               maxlength="255" required="required" autofocus="autofocus" />
    </div>
    <div class="form-group">
        <label class="control-label" for="description">Description</label>
        <input id="description" type="text" name="val[description]" class="form-control"
               maxlength="255" required="required" />
    </div>
    <div class="form-group">
        <label class="control-label" for="price">Price (USD)</label>
        <input id="price" type="number" name="val[price]" class="form-control" step=".01" required="required" />
    </div>
    <div class="form-group">
        <input type="hidden" name="val[token]" class="form-control" required="required" value="{$token}"/>
    </div>
    <div class="form-group">
        <a class="btn btn-default" href="/admin-dashboard/packages">Cancel</a>
        <button type="submit" class="btn btn-primary" name="_submit">Add Shipping Type</button>
    </div>
</form>

<script>
    // Autofocus on input
    document.getElementById('name').focus();
</script>
{/if}
