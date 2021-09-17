{if $error}
<div class="alert alert-danger" role="alert">{$error}</div>
<a class="btn btn-default" href="/admin-dashboard/packages">Go to Packages</a>
{elseif $csrfError}
<div class="alert alert-danger" role="alert"><strong>CSRF detected!</strong> For your security, please logout immediately and report this incident to a developer.</div>
{else}

<p>Are you sure you want to delete this package?</p>

<form method="post">
    <div class="form-group">
        <input type="hidden" name="val[token]" class="form-control" required="required" value="{$token}"/>
    </div>
    <div class="form-group">
        <a class="btn btn-default" href="/admin-dashboard/packages">Cancel</a>
        <button type="submit" class="btn btn-danger" name="_submit">Delete</button>
    </div>
</form>
{/if}
