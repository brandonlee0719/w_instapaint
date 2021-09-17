<div class="message">
    {_p var='upgrade_site'}
</div>
<form method="post" action="{url link='admincp.trial'}" class="form">
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="form-group">
                <label]>{_p('License ID')}</label>
                <input type="text" name="val[license_id]">
            </div>
            <div class="form-group">
                <label>{_p('License Key')}</label>
                <input type="text" name="val[license_key]">
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="{_p('Enter License Key')}">
            </div>
        </div>
    </div>
</form>