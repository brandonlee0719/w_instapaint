<?php
/**
 * Script for testing and previewing formatted request data.
 */
error_reporting( -1 );
ini_set( 'display_errors', 1 );
ini_set( 'display_startup_errors', 1 );
require( '../autoloader.php' );

// send AJAX resopnse
if( $_SERVER['REQUEST_METHOD'] !== 'GET' )
{
    // parse incoming request data
    $request = new Restful\Parser();
    $request->parse();

    // handler for uploaded files
    $files = new Restful\Files();
    $files->blacklist( 'html', 'js', 'css', 'php', 'phtml', 'cgi', 'sh', 'py', 'exe', 'app' );
    $files->parse();

    // form options
    $task    = isset( $_REQUEST['scriptTask'] ) ? $_REQUEST['scriptTask'] : '';
    $output  = '';

    // show the new formatted request data
    if( $task === 'show' )
    {
        $output  = "REQUEST-DATA: \n\n";
        $output .= print_r( $_REQUEST, true ) . "\n\n";

        $output .= "FILES-DATA: \n\n";
        $output .= print_r( $files->getParsed(), true ) . "\n\n";
    }

    // loop through files and show only the names
    if( $task === 'loop' )
    {
        $output   = "UPLOAD-FILENAMES: \n\n";
        $getnames = function( $file )
        {
            global $output;
            $output .= $file['name'] . "\n";
        };
        $files->loopFiles( 'files.single', $getnames );
        $files->loopFiles( 'files.multiple', $getnames );
    }

    // save all uploaded files to a folder
    if( $task === 'save' )
    {
        $output   = "UPLOAD-SAVE-RESULTS: \n\n";
        $single   = $files->moveFiles( 'files.single', __DIR__.'/uploads' );
        $multiple = $files->moveFiles( 'files.multiple', __DIR__.'/uploads' );

        foreach( array_merge( $single, $multiple ) as $file )
        {
            $result  = !empty( $file['error'] ) ? $file['error'] : $file['new_name'];
            $output .= 'File: '. $file['name'] ."\n";
            $output .= 'Result: '. $result ."\n\n";
        }
    }

    // send response output
    header( 'Content-Type: text/plain', true, 200 );
    die( $output );
}

// get list of uploaded files
$uploads = '';
$expire  = time() - ( 60 * 60 * 5 );
foreach( glob( __DIR__.'/uploads/*.*' ) as $file )
{
    if( @filectime( $file ) < $expire ){ unlink( $file ); continue; }
    $flink =  str_replace( __DIR__, '', $file );
    $uploads .= '<li><a href=".'. $flink .'" target="_blank">'. $flink .'</a></li> ';
}
?>
<!DOCTYPE html>
<html>
<head>

    <title>ButterFiles Test</title>
    <meta charset="utf-8" />
    <style type="text/css">
    html, body { margin: 0; padding: 0; background-color: #f0f0f0; font-family: monospace; font-size: 14px; line-height: 18px; }
    h1, h2, h3, section, pre { margin: 0 0 20px 0; padding: 0; }
    form { display: flex; flex-direction: column; }
    fieldset { display: block; margin: 0 0 10px 0; border: 1px dashed rgba(0,0,0,0.25); }
    .container { margin: 0; padding: 20px; }
    .code { margin: 0 0 20px 0; padding: 10px; background-color: #eec; }
    </style>

</head>
<body>

    <div class="container">

        <h3>Request Form</h3>
        <section>
            <form id="request-form" action="<?= $_SERVER['PHP_SELF'] ?>" enctype="multipart/form-data">
                <fieldset>
                    <legend>Request Method</legend>
                    <select name="requestMethod">
                        <option value="POST">POST</option>
                        <option value="PUT">PUT</option>
                        <option value="PATCH">PATCH</option>
                        <option value="DELETE">DELETE</option>
                    </select>
                </fieldset>
                <fieldset>
                    <legend>Script Task</legend>
                    <select name="scriptTask">
                        <option value="show">Preview new request data</option>
                        <option value="loop">Loop file names</option>
                        <option value="save">Save files to a folder</option>
                    </select>
                </fieldset>
                <fieldset>
                    <legend>Single File Upload</legend>
                    <input type="file" name="files[single]" />
                </fieldset>
                <fieldset>
                    <legend>Multiple Files Upload</legend>
                    <input type="file" name="files[multiple][]" multiple />
                </fieldset>
                <fieldset>
                    <legend>Send Values</legend>
                    <input type="hidden" name="multi[level][nested][value]" value="foo" />
                    <input type="submit" value="Submit" />
                </fieldset>
            </form>
        </section>

        <h3>Request Output</h3>
        <pre id="output-wrap" class="code">Use the form to send a request and preview a response here.</pre>

        <h3>Uploaded Files</h3>
        <ul>
            <li><a href="#" onclick="top.location.reload( true ); return false;">Reload</a></li>
            <?= $uploads ?>
        </ul>

    </div>

    <script type="text/javascript">
    document.getElementById( 'request-form' ).addEventListener( 'submit', function( e )
    {
        e.preventDefault();

        var form   = this,
            code   = document.getElementById( 'output-wrap' ),
            method = form.requestMethod.value || '',
            url    = form.action || '',
            xhr    = null;

        if( window.XMLHttpRequest ){ xhr = new XMLHttpRequest(); } else
        if( window.ActiveXObject ) { xhr = new ActiveXObject( 'Microsoft.XMLHTTP' ); }
        if( xhr )
        {
            xhr.open( method, url, true );
            xhr.onreadystatechange = function()
            {
                if( xhr.readyState != 4 ) return;
                if( xhr.status != 200 )
                {
                    alert( 'Status code ('+ xhr.status +'): ' + ( xhr.responseText || 'No response' ) );
                    return;
                }
                code.innerHTML = xhr.responseText || 'Empty response.';
            }
            xhr.send( new FormData( form ) );
        }
    });
    </script>

</body>
</html>