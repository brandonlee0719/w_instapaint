<?php
defined('PHPFOX') or exit('NO DICE!');
?>

<form method="post" action="#" class="ajax_post form">
    <div class="panel panel-default">
        <div class="panel-heading">
            {_p var='Short Url'}
        </div>
        <div class="panel-body">

            {if $hasRewrite}
            <p>
                {_p var='short_urls_are_up_and_running_dot_good_job'}!
            </p>
            {else}
            {if $hasHtaccess}
            <p>
                - {_p var='add_the_following_at_the_start_of_your_strong_dothtaccess_strong_file'}:
            </p>>
            {else}
            <p> - {_p var='create_a_new_file_called_strong_dothtaccess_strong_and_add_the_following'}:</p>
            {/if}
            </p>
            <p>- {_p var='continue_below_if_your_strong_dothtaccess_strong_file_is_ready'}.</p>
            <p>- {_p var='visit_here_for_more_information' here="https://docs.phpfox.com/display/FOX4MAN/Enabling+short+url"}.</p>
            <pre style="margin-bottom:30px; background:#0c0c0c; text-indent:0px; color:#fff; padding:10px; font-family:monospace; font-size:13px;">
# START phpFox Rewrite
Options -Indexes
&lt;IfModule mod_rewrite.c&gt;
    RewriteEngine On
    RewriteBase {param var='core.folder_original'}

    {literal}
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^(file)/(.*) PF.Base/$1/$2
    RewriteRule ^themes/default/(.*) PF.Base/theme/default/$1
    RewriteRule ^(static|theme|module)/(.*) PF.Base/$1/$2
    RewriteRule ^(Apps|themes)/(.*) PF.Site/$1/$2
    {/literal}

    {literal}
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule . index.php [L]
    {/literal}

&lt;/IfModule&gt;
# END phpFox Rewrite
</pre>{/if}
        </div>
        {if !$hasRewrite}
        <div class="panel-footer">
            <input type="submit" class="btn btn-primary" value="{_p var='enable_short_urls'}">
        </div>
        {/if}
    </div>
</form>