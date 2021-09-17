<?php
if (isset($_POST['license_id'])) {
    $sLicensePath = realpath(__DIR__ . '/../file/settings/license.sett.php');
    try {
        if (!file_exists($sLicensePath)) {
            throw new Exception('PHPfox does not seem to be installed. Odd...');
        }
        require($sLicensePath);

        if ($_POST['license_id'] != PHPFOX_LICENSE_ID) {
            throw new Exception('License ID does not match.');
        }

        if ($_POST['license_key'] != PHPFOX_LICENSE_KEY) {
            throw new Exception('License Key does not match.');
        }
        if (isset($_POST['download']) && $_POST['download']) {
            header('Content-type: application/text; charset=utf-8');
            header('Content-disposition: filename="main.log"');
        }
        $path = realpath(__DIR__ . '/../file/log/main.log');
        if (file_exists($path)) {
            echo file_get_contents($path);
        } else {
            echo 'No log found. Please be sure file ' . $path . ' is writable';
        }
        exit();
    } catch (Exception $e) {
        $sError = '<div class="alert alert-danger">' . $e->getMessage() . '</div>';
    }
}
?>
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en">
<head>
    <title>phpFox Debug</title>
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
</head>
<body>
<div class="navbar navbar-default">
    <a class="navbar-brand" href="#">phpFox Log Checking</a>
</div>
<div class="container">
    <div class="col-md-8">
        <?php if (isset($sError)) :?>
            <?php echo $sError; ?>
        <?php endif; ?>
        <form method="post" action="log.php" class="" enctype="multipart/form-data">
            <div class="form-group">
                <label>License ID:</label>
                <input type="text" name="license_id" class="form-control">
            </div>
            <div class="form-group">
                <label>License Key:</label>
                <input type="text" name="license_key" class="form-control">
            </div>
            <input type="submit" class="btn btn-primary" value="View Log" name="view">
            <input type="submit" class="btn btn-success" value="Download" name="download">
        </form>
    </div>

</div>
<script src="//code.jquery.com/jquery-1.11.3.min.js"></script>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
</body>
</html>