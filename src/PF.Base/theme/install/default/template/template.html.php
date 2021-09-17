<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="{$sLocaleDirection}" lang="{$sLocaleCode}">
	<head>
		<title>{title}</title>
        {header}
	</head>
	<body>
        <div id="install_holder">
            {if (isset($bIsUprade) && $bIsUprade)}
            <div id="is-upgrade"></div>
            {/if}
            <div id="header">
                phpFox <span>{$sCurrentVersion}</span>
            </div>
            <div id="installer">
                {if isset($requirementErrors)}
                <form method="post" action="#start" class="form no_ajax">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Requirements
                        </div>
                        <div class="panel-body">
                            <ol class="alert alert-danger">
                                {foreach from=$requirementErrors item=error}
                                <li class="">
                                    {$error}
                                </li>
                                {/foreach}
                            </ol>
                        </div>
                        <div class="panel-footer">
                            <input type="submit" value="Try Again" class="btn btn-danger" />
                        </div>
                    </div>
                </form>
                {else}
                <div class="process">Loading installer<i class="fa fa-spin fa-circle-o-notch"></i></div>
                {/if}
            </div>
        </div>
        {loadjs}

	</body>
</html>