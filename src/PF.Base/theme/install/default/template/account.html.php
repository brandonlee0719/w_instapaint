<?php
defined('PHPFOX') or exit('NO DICE!');
/**
 * @author Neil J.<neil@phpfox.com>
 */
?>
<form method="post" action="#final" id="js_form" class="form">
    <h1>Administrator Account</h1>
    <div id="errors" class="hide"></div>
    <div class="form-group">
        <label for="email">Email</label>
        <input autofocus required class="form-control" placeholder="Enter your email" type="email" name="val[email]" id="email" value="{value type='input' id='email'}" size="30" />
    </div>
    <div class="form-group">
        <label for="password">Password</label>
        <input class="form-control" placeholder="Enter your password" required type="password" name="val[password]" id="password" value="{value type='input' id='password'}" size="30" autocomplete="off" />
    </div>
    <div class="help-block">
        If you encounter any problem, please follow our instruction in <a href="https://docs.phpfox.com/display/FOX4MAN/Installing+phpFox" target="_blank">this help topic</a> then try again.
    </div>
    <input type="submit" value="Continue" class="hide" />
</form>