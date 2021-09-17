<?php
/**
 * Provides a better way for working with the PHP _FILES array.
 *
 * @author     Rainner Lins | http://rainnerlins.com
 * @copyright  All Rights Reserved
 * @license    MIT
 */
namespace Restful;

class Files {

    /**
     * upload_max_filesize
     * @var integer
     */
    protected $maxsize = 0;

    /**
     * post_max_size
     * @var integer
     */
    protected $maxpost = 0;

    /**
     * List of file extension to skip
     * @var array
     */
    protected $skip = array();

    /**
     * PHP FILES array
     * @var array
     */
    protected $files = array();

    /**
     * Constructor
     * @return null
     */
    public function __construct()
    {
        $this->maxsize = ini_get( 'upload_max_filesize' );
        $this->maxpost = ini_get( 'post_max_size' );
    }

    /**
     * Set a list of file extensions to skip when handling files
     * @param  [string] List of extension types as arguments
     * @return null
     */
    public function blacklist()
    {
        $this->skip = array();

        foreach( func_get_args() as $extension )
        {
            if( !empty( $extension ) && is_string( $extension ) )
            {
                $this->skip[] = $this->_ext( $extension );
            }
        }
    }

    /**
     * Parses a given FILES array
     * @return null
     */
    public function parse()
    {
        $this->files = array();

        if( is_array( $_FILES ) && !empty( $_FILES ) )
        {
            // 1. convert files array into an URL encoded string
            $query  = urldecode( http_build_query( $_FILES, '', '&' ) );
            $lines  = explode( '&', $query );
            $param  = '';
            $build  = '';

            // 2. loop each key=value as individual line
            foreach( $lines as $line )
            {
                // 3. split line into separate key and value
                $parts = explode( '=', $line );
                $key   = isset( $parts[0] ) ? $parts[0] : '';
                $value = isset( $parts[1] ) ? $parts[1] : '';

                // 4. extract the fileinfo property from the key
                if( preg_match( '/(\[(name|type|tmp_name|error|size)\])/', $key, $prop ) )
                {
                    // 5. remove fileinfo prop from key
                    $key = str_replace( $prop[1], '', $key );

                    // 6. add a [0] to the end of key if dealing with a single file
                    if( !preg_match( '/\[[0-9]+\]$/', $key ) ) $key .= '[0]';

                    // 7. add the fileinfo prop to the end of key
                    $key .= $prop[1];

                    // 8. clean the value and add to final string
                    $param  = preg_replace( '/[^\w]+/', '', $prop[1] );
                    $value  = $this->_value( $param, $value );
                    $build .= $key .'='. $value .'&';
                }
            }
            // 9. convert the new encoded values back into an array
            if( !empty( $build ) )
            {
                parse_str( $build, $this->files );
            }
        }
    }

    /**
     * Returns the new parsed FILES array
     * @return array
     */
    public function getParsed()
    {
        return $this->files;
    }

    /**
     * Returns a list of uploaded files for $key using dot-notation
     * @param  string $key  Key string to look for
     * @return array        Enumerated array of $key files, or empty array
     */
    public function getFiles( $key='' )
    {
        $key   = trim( preg_replace( '/[^\w\-\.]+/', '', $key ), '_-.' );
        $files = array();

        if( !empty( $key ) )
        {
            $keys  = explode( '.', $key );
            $files = $this->files;

            while( $keys )
            {
                $next = array_shift( $keys );
                if( is_array( $files[ $next ] ) ){ $files = $files[ $next ]; }
                else{ return array(); }
            }
        }
        return $files;
    }

    /**
     * Move uploaded files for $key to a root directory.
     * @param  string  $key      Key string to look for files
     * @param  string  $dirname  Base folder to put files into
     * @param  boolean $organize Optional flag to add /yyyy/mm/dd sub folders
     * @return array             List of files, each with a new property [new_name], if moved
     */
    public function moveFiles( $key='', $dirname='', $organize=false )
    {
        $files   = $this->getFiles( $key );
        $subdirs = $organize ? date( 'Y/m/d' ) : '';
        $dirname = $this->_dirname( $dirname.'/'.$subdirs );

        if( !empty( $files ) && ( is_dir( $dirname ) || mkdir( $dirname, 0777, true ) ) )
        {
            foreach( $files as $i => $file )
            {
                if( empty( $file['name'] ) )
                {
                    $files[ $i ]['error'] = 'The uploaded file must have a valid file name.';
                }
                else if( in_array( $this->_ext( $file['name'] ), $this->skip ) )
                {
                    $files[ $i ]['error'] = 'The uploaded file type is not allowed by this script.';
                }
                else if( !is_file( $file['tmp_name'] ) || !filesize( $file['tmp_name'] ) )
                {
                    $files[ $i ]['error'] = 'The uploaded file was not transfered to the server.';
                }
                else if( !is_writable( $dirname ) )
                {
                    $files[ $i ]['error'] = 'This application does not have write access to the destination folder.';
                }
                else if( !copy( $file['tmp_name'], $dirname.'/'.$file['name'] ) )
                {
                    $files[ $i ]['error'] = 'The uploaded file could not be saved to final destination.';
                }
                else
                {
                    $files[ $i ]['error']    = '';
                    $files[ $i ]['new_name'] = $dirname.'/'.$file['name'];
                    @unlink( $file['tmp_name'] );
                }
            }
        }
        return $files;
    }

    /**
     * Loop uploaded files for $key and executes a callback function on each
     * @param  string   $key       Key string to look for files
     * @param  function $callback  Closure to execute on each file on the list
     * @return null
     */
    public function loopFiles( $key='', $callback=null )
    {
        $files = $this->getFiles( $key );

        if( !empty( $files ) && is_callable( $callback ) )
        {
            foreach( $files as $i => $file )
            {
                call_user_func( $callback, $file );
            }
        }
    }

    /**
     * Sanitizes a value for different param types
     */
    private function _value( $param='', $value='' )
    {
        if( $param === 'name' )
        {
            $value = trim( preg_replace( '/[^\w\-\.]+/i', '_', $value ) );
        }
        else if( $param === 'type' || $param === 'tmp_name' )
        {
            $value = trim( str_replace( '\\', '/', $value ) );
        }
        else if( $param === 'size' && is_numeric( $value ) )
        {
            $value = intval( $value );
        }
        else if( $param === 'error' && is_numeric( $value ) )
        {
            switch( intval( $value ) )
            {
                case UPLOAD_ERR_INI_SIZE   : $value = "The file size exceeds the (upload_max_filesize: ".$this->maxsize.") server limit."; break;
                case UPLOAD_ERR_FORM_SIZE  : $value = "The file size exceeds the (max_file_size: ".$this->maxpost.") http/post limit."; break;
                case UPLOAD_ERR_PARTIAL    : $value = "The file was only partially uploaded and could not be saved."; break;
                case UPLOAD_ERR_NO_FILE    : $value = "The server did not receive any file contents to be saved."; break;
                case UPLOAD_ERR_NO_TMP_DIR : $value = "The server had no temporary folder to store the file."; break;
                case UPLOAD_ERR_CANT_WRITE : $value = "The server does not have permission to copy the file contents."; break;
                case UPLOAD_ERR_EXTENSION  : $value = "A server script extension stopped the file upload."; break;
                default:                     $value = "";
            }
        }
        return $value;
    }

    /**
     * Sanitizes a directory path value
     */
    private function _dirname( $value='' )
    {
        $value = str_replace( '\\', '/', trim( $value ) );
        $value = preg_replace( '/\/\/+/', '/', $value );
        return rtrim( $value, '/' );
    }

    /**
     * Sanitizes a file extension value
     */
    public function _ext( $value='' )
    {
        $parts = explode( '.', trim( $value ) );
        $value = trim( array_pop( $parts ) );
        $value = preg_replace( '/[^a-zA-Z0-9]+/', '', $value );
        return strtolower( $value );
    }

}
