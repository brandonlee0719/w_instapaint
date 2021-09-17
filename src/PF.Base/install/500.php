<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Installation Failed</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous">
</head>
<body>
    <div class="container">
        <br/>
        <div class="col-sm-12">
            <h1 class="display-6">Installation Failed</h1>
            <div class="alert alert-warning">
                Sorry, there are some errors occurred while installing phpFox.<br/>
                Please <a href="https://clients.phpfox.com/submitticket.php?step=2&deptid=1" target="_blank">contact us</a> to get help. Remember to send us the following error log.
            </div>
        </div>
        <div class="col-sm-12">
            <form method="post" action="https://clients.phpfox.com/submitticket.php?step=2&deptid=1">
            <h3>Error Log</h3>
            <textarea class="form-control" rows="20" name="error_content" onclick="this.select()">
            <?php
              if(file_exists($filename = '../../PF.Base/file/log/installation.log')){
                  echo file_get_contents($filename);
              } ?>
            </textarea>
            </form>
        </div>
    </div>
<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js" integrity="sha384-h0AbiXch4ZDo7tp9hKZ4TsHbi047NrKGLO3SEJAg45jXxnGIfYzk4Si90RDIqNm1" crossorigin="anonymous"></script>
</body>
</html>
