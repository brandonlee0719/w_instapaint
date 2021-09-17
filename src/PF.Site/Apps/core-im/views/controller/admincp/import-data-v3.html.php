{if isset($bNoImTable)}
<div class="panel panel-default">
    <div class="panel-body">
        <div class="alert alert-danger">
            {_p var='table_im_not_found' table=$sTableIm}
        </div>
    </div>
</div>
{else}
<form method="post">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">{_p var='import_data_from_v3'}</div>
        </div>
        <div class="panel-body">
            {if isset($bNoRedis)}
            <div class="alert alert-danger">
                {_p var='import_directly_not_working'}
            </div>
            {else}
            <p class="help-block">{_p var='import_data_from_v3_instruction'}</p>
            <div class="form-group">
                <div class="table_left">
                    <label for="redis_host">{_p var='redis_host'}</label>
                </div>
                <div class="table_right">
                    <input type="text" id="redis_host" name="redis_host" class="form-control" autofocus>
                </div>
            </div>
            <div class="form-group">
                <div class="table_left">
                    <label for="redis_port">{_p var='redis_port'}</label>
                </div>
                <div class="table_right">
                    <input type="text" id="redis_port" name="redis_port" class="form-control">
                </div>
            </div>
            <div class="form-group">
                <div class="table_left">
                    <label for="redis_password">{_p var='redis_password'}</label>
                </div>
                <div class="table_right">
                    <input type="text" id="redis_password" name="redis_password" class="form-control">
                </div>
            </div>
            <div class="form-group">
                <input type="submit" value="{_p var='import'}" name="submit" class="btn btn-primary">
                <input type="reset" value="{_p var='reset'}" class="btn btn-warning">
            </div>
            {/if}
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">{_p var='import_data_manual'}</div>
        </div>
        <div class="panel-body">
            <p class="help-block">{_p var='import_data_manual_instruction'}</p>
            <input type="submit" value="{_p var='export_json'}" class="btn btn-primary" name="export_json">
        </div>
    </div>
</form>
{/if}