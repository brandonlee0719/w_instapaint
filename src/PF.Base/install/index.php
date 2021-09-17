<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en">
<head>
    <?php
    $protocol = 'http';
    $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
        $protocol = 'https';
    } elseif (isset($_SERVER['SERVER_PORT']) and $_SERVER['SERVER_PORT'] == 443) {
        $protocol = 'https';
    } elseif (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) and $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
        $protocol = 'https';
    } elseif (isset($_SERVER['HTTP_CF_VISITOR']) and strpos($_SERVER['HTTP_CF_VISITOR'], 'https')) {
        $protocol = 'https';
    }
    $parts = explode('index.php', $_SERVER['PHP_SELF']);
    $baseUrl = $protocol . '://' . $host . $parts[0];
    $bIsUpgrade = defined('PHPFOX_IS_UPGRADE') && PHPFOX_IS_UPGRADE;
    ?>
    <title>phpFox 4.6.0 - Powered By PHPFox</title>
    <script>var BasePath = '<?php echo $baseUrl ?>';</script>
    <link href="<?php echo $baseUrl ?>PF.Base/theme/install/default/style/default/css/font-awesome.min.css?_=<?php time() ?>"
          rel="stylesheet">
    <link href="<?php echo $baseUrl ?>PF.Base/theme/install/default/style/default/css/bootstrap.min.css?_=<?php time() ?>"
          rel="stylesheet">
    <link href="<?php echo $baseUrl ?>PF.Base/theme/install/default/style/default/css/layout.css?_=<?php time() ?>"
          rel="stylesheet">
    <?php
    $rootDir = dirname(dirname(dirname(__FILE__)));
    ?>
    <meta name="root" content="<?php echo $rootDir ?>"/>
</head>
<body class="<?php echo $bIsUpgrade?'in-upgrade':'in-install' ?>">
<div id="install_holder">
    <div id="header">
        phpFox <span class="">
            <strong class="">4.6.0</strong>
        </span>
    </div>
    <div id="installer">
        <div class="panel panel-default">
            <div class="row">
                <div class="col-sm-3 col-xs-4">
                    <ul class="steps">
                        <li class="active" id="step_requirement">
                            <i class="fa"></i> Requirements
                        </li>
                        <li class="" id="step_key">
                            <i class="fa"></i> License
                        </li>
                        <li class="only-install" id="step_configuration">
                            <i class="fa"></i> Configuration
                        </li>
                        <li class="only-upgrade" id="step_ftp">
                            <i class="fa"></i> Configuration
                        </li>
                        <li class="" id="step_import">
                            <i class="fa"></i> Processing
                        </li>
                        <li class="" id="step_all_done">
                            Done
                        </li>
                    </ul>
                </div>
                <div class="col-sm-9 col-xs-8">
                    <div id="loading" class="hide">
                        <i class="fa fa-spinner fa-spin" style="font-size:200%"></i>
                    </div>
                    <div id="installer-content" class="installer-content">
                        <h1>phpFox Requirements.</h1>
                        <div id="errors" class="hide"></div>
                        <table class="table check-requirements">
                            <?php $error = version_compare(phpversion(), '5.5', '<') ?>
                            <tr class="<?php echo $error ? 'text-danger has-error' : '' ?>">
                                <td>
                                    PHP version >= 5.5
                                </td>
                                <td width="40">
                                    <i class="fa <?php echo $error ? 'fa-remove text-danger'
                                        : 'fa-check text-success' ?>"></i>
                                </td>
                            </tr>
                            <?php $error = !function_exists('mb_substr') ?>
                            <tr class="<?php echo $error ? 'text-danger has-error' : '' ?>">
                                <td>
                                    <a title="View more information" class="as-text" href="http://php.net/manual/en/book.mbstring.php"
                                       target="_blank">Support multi-byte string library
                                    </a>
                                </td>
                                <td width="40">
                                    <i class="fa <?php echo $error ? 'fa-remove text-danger'
                                        : 'fa-check text-success' ?>"></i>
                                </td>
                            </tr>
                            <?php $error = !function_exists('xml_set_element_handler') ?>
                            <tr class="<?php echo $error ? 'text-danger has-error' : '' ?>">
                                <td>
                                    <a title="View more information" class="as-text" href="http://php.net/manual/en/book.xml.php" target="_blank">
                                        Support XML library
                                        </a>
                                </td>
                                <td>
                                    <i class="fa <?php echo $error ? 'fa-remove text-danger'
                                        : 'fa-check text-success' ?>"></i>
                                </td>
                            </tr>
                            <?php $error = !(extension_loaded('gd') && function_exists('gd_info')) ?>
                            <tr class="<?php echo $error ? 'text-danger has-error' : '' ?>">
                                <td>
                                    <a title="View more information" class="as-text" href="http://php.net/manual/en/book.image.php" target="_blank">
                                        Support image Process library
                                    </a>
                                </td>
                                <td>
                                    <i class="fa <?php echo $error ? 'fa-remove text-danger'
                                        : 'fa-check text-success' ?>"></i>
                                </td>
                            </tr>
                            <?php $error = !function_exists('mysqli_connect') ?>
                            <tr class="<?php echo $error ? 'text-danger has-error' : '' ?>">
                                <td><a title="View more information" class="as-text" href="http://php.net/manual/en/function.mysqli-connect.php"
                                       target="_blank">
                                        Support mysqli driver.
                                    </a></td>
                                <td>
                                    <i class="fa <?php echo $error ? 'fa-remove text-danger'
                                        : 'fa-check text-success' ?>"></i>
                                </td>
                            </tr>
                            <?php $error = !class_exists('ZipArchive') ?>
                            <tr class="<?php echo $error ? 'text-danger has-error' : '' ?>">
                                <td>
                                    <a title="View more information" class="as-text" href="http://php.net/manual/en/class.ziparchive.php"
                                       target="_blank">Support file archive, compressed with Zip.</a></td>
                                <td>
                                    <i class="fa <?php echo $error ? 'fa-remove text-danger'
                                        : 'fa-check text-success' ?>"></i>
                                </td>
                            </tr>
                            <?php $error = !function_exists('exec') ?>
                            <tr class="<?php echo $error ? 'text-danger has-error' : '' ?>">
                                <td>
                                    <a title="View more information" class="as-text" href="http://php.net/manual/en/book.exec.php" target="_blank">
                                        Support execute shell command.</a>
                                </td>
                                <td>
                                    <i class="fa <?php echo $error ? 'fa-remove text-danger'
                                        : 'fa-check text-success' ?>"></i>
                                </td>
                            </tr>
                            <?php $error = !(extension_loaded('curl') && function_exists('curl_init')) ?>
                            <tr class="<?php echo $error ? 'text-danger has-error' : '' ?>">
                                <td>
                                    <a title="View more information" class="as-text" href="http://php.net/manual/en/book.curl.php" target="_blank">
                                        Support libcurl, connect to remote service.
                                    </a>
                                </td>
                                <td>
                                    <i class="fa <?php echo $error ? 'fa-remove text-danger'
                                        : 'fa-check text-success' ?>"></i>
                                </td>
                            </tr>
                        </table>
                        <div class="help-block">
                            If you encounter any problem, please follow our instruction in <a href="https://docs.phpfox.com/display/FOX4MAN/Installing+phpFox" target="_blank">this help topic</a> then try again.
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel-footer text-right">
                <button name="btn_ok" id="btn_ok" type="button" class="btn btn-success" onclick="installer.continue()">Continue</button>
            </div>
        </div>
    </div>
    <div id="log_area" class="hide">
    </div>
    <?php if (defined('PHPFOX_IS_UPGRADE')): ?>
    <div id="is-upgrade" class="hide"></div>
    <?php endif; ?>
</div>
<script src="<?php echo $baseUrl ?>PF.Base/static/jscript/jquery/jquery.js?_=<?php time() ?>"></script>
<script src="<?php echo $baseUrl ?>PF.Base/static/jscript/bootstrap.min.js?_=<?php time() ?>"></script>
<script src="<?php echo $baseUrl ?>PF.Base/theme/install/default/style/default/jscript/installer.js?_=<?php time() ?>">
</script>
<script type="text/javascript">
    installer.start();
</script>
</body>
</html>