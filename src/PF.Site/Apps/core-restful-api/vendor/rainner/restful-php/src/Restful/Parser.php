<?php
/**
 * Parses the HTTP raw input string for different content types.
 *
 * @author     Rainner Lins | http://rainnerlins.com
 * @copyright  All Rights Reserved
 * @license    MIT
 */
namespace Restful;

class Parser {

    /**
     * Request method verb
     * @var string
     */
    protected $method = '';

    /**
     * Request content-type
     * @var string
     */
    protected $ctype = '';

    /**
     * Request boundary for multipart/form data
     * @var string
     */
    protected $boundary = '';

    /**
     * Request raw input body data
     * @var string
     */
    protected $input = '';

    /**
     * Constructor
     * @return null
     */
    public function __construct()
    {
        $this->resolveMethod();
        $this->resolveInput();
    }

    /**
     * Resolve request method verb
     * @return null
     */
    public function resolveMethod()
    {
        $value = $this->_value( @$_SERVER['REQUEST_METHOD'], '' );
        $value = $this->_value( @$_SERVER['HTTP_X_HTTP_METHOD'], $value );
        $this->method = strtolower( trim( $value ) );
    }

    /**
     * Resolve content-type and other input data
     * @return null
     */
    public function resolveInput()
    {
        $value = $this->_value( @$_SERVER['CONTENT_TYPE'], '' );
        $value = $this->_value( @$_SERVER['HTTP_CONTENT_TYPE'], $value );
        $value = $this->_value( @$_SERVER['HTTP_X_CONTENT_TYPE'], $value );
        $parts = array();

        if( !empty( $value ) )
        {
            $value = 'ctype='. $value;
            $value = preg_replace( '/\;\s+/', '&', $value );
            parse_str( $value, $parts );
        }
        $this->ctype    = $this->_value( @$parts['ctype'], '' );
        $this->boundary = $this->_value( @$parts['boundary'], '' );
        $this->input    = trim( file_get_contents( 'php://input' ) );
    }

    /**
     * Set the content-type to use
     * @param  string $ctype
     * @return null
     */
    public function setContentType( $ctype='' )
    {
        if( !empty( $ctype ) && is_string( $ctype ) )
        {
            $parts = explode( ';', trim( $ctype ) );
            $this->ctype = $this->_value( @$parts[0], '' );
        }
    }

    /**
     * Set the form-data boundary string to use
     * @param  string $boundary
     * @return null
     */
    public function setBoundary( $boundary='' )
    {
        if( !empty( $boundary ) && is_string( $boundary ) )
        {
            $this->boundary = trim( $boundary );
        }
    }

    /**
     * Set the raw input string to parse
     * @param  string $input
     * @return null
     */
    public function setRawInput( $input='' )
    {
        if( !empty( $input ) && is_string( $input ) )
        {
            $this->input = trim( $input );
        }
    }

    /**
     * Parse the input data for different content types
     * @return null
     */
    public function parse()
    {
        if( $this->method === 'post' )
        {
            $_REQUEST = array_merge( $_GET, $_POST );
            return true;
        }
        if( !empty( $this->ctype ) && !empty( $this->input ) )
        {
            if( strpos( $this->ctype, 'plain' ) !== false )
            {
                return $this->_parsePlain();
            }
            else if( strpos( $this->ctype, 'json' ) !== false )
            {
                return $this->_parseJson();
            }
            else if( strpos( $this->ctype, 'xml' ) !== false )
            {
                return $this->_parseXml();
            }
            else if( strpos( $this->ctype, 'html' ) !== false )
            {
                return $this->_parseXml();
            }
            else if( strpos( $this->ctype, 'form-urlencoded' ) !== false )
            {
                return $this->_parseEncoded();
            }
            else if( strpos( $this->ctype, 'form-data' ) !== false )
            {
                return $this->_parseForm();
            }
        }
        return false;
    }

    /**
     * Parse plain text as INI data
     */
    private function _parsePlain()
    {
        $data = @parse_ini_string( $this->input, true );

        if( is_array( $data ) )
        {
            $_REQUEST = array_merge( $_GET, $data );
            return true;
        }
        return false;
    }

    /**
     * Parse JSON encoded data
     */
    private function _parseJson()
    {
        $data = @json_decode( $this->input, true );

        if( is_array( $data ) )
        {
            $_REQUEST = array_merge( $_GET, $this->_data( $data ) );
            return true;
        }
        return false;
    }

    /**
     * Parse XML encoded data
     */
    private function _parseXml()
    {
        $temp = @simplexml_load_string( $this->input, "SimpleXMLElement", LIBXML_NOCDATA );
        $json = @json_encode( $temp );
        $data = @json_decode( $json, true );

        if( is_array( $data ) )
        {
            $_REQUEST = array_merge( $_GET, $this->_data( $data ) );
            return true;
        }
        return false;
    }

    /**
     * Parse URL encoded data
     */
    private function _parseEncoded()
    {
        @parse_str( $this->input, $data );

        if( is_array( $data ) )
        {
            $_REQUEST = array_merge( $_GET, $data );
            return true;
        }
        return false;
    }

    /**
     * Parse FORM encoded data
     */
    private function _parseForm()
    {
        if( !empty( $this->boundary ) )
        {
            $chunks  = @preg_split( '/[\-]+'.$this->boundary.'(\-\-)?/', $this->input, -1, PREG_SPLIT_NO_EMPTY );
            $request = array();
            $files   = array();
            $nd      = 0;
            $nf      = 0;

            if( is_array( $chunks ) )
            {
                foreach( $chunks as $index => $chunk )
                {
                    $chunk  = ltrim( $chunk, "-\r\n\t\s " );
                    $lines  = explode( "\r\n", $chunk );
                    $levels = '';
                    $name   = '';
                    $file   = '';
                    $type   = '';
                    $value  = '';
                    $path   = '';
                    $copy   = false;

                    // skip empty chunks
                    if( empty( $chunk ) || empty( $lines ) ) continue;

                    // extract name/filename
                    if( strpos( $lines[0], 'Content-Disposition' ) !== false )
                    {
                        $line = $this->_line( array_shift( $lines ) );
                        $name = $this->_value( @$line['name'], '', true );
                        $file = $this->_value( @$line['filename'], '', true );
                    }
                    // extract content-type
                    if( strpos( $lines[0], 'Content-Type' ) !== false )
                    {
                        $line = $this->_line( array_shift( $lines ) );
                        $type = $this->_value( @$line['content'], '', true );
                    }
                    // rebuild value
                    $value = trim( implode( "\r\n", $lines ) );

                    // FILES data
                    if( !empty( $type ) )
                    {
                        if( !empty( $value ) )
                        {
                            $path = str_replace( '\\', '/', sys_get_temp_dir() .'/php'. substr( sha1( rand() ), 0, 6 ) );
                            $copy = file_put_contents( $path, $value );
                        }
                        if( preg_match( '/(\[.*?\])$/', $name, $tmp ) )
                        {
                            $name   = str_replace( $tmp[1], '', $name );
                            $levels = preg_replace( '/\[\]/', '['.$nf.']', $tmp[1] );
                        }
                        $files[ $name.'[name]'.$levels ]     = $file;
                        $files[ $name.'[type]'.$levels ]     = $type;
                        $files[ $name.'[tmp_name]'.$levels ] = $path;
                        $files[ $name.'[error]'.$levels ]    = !empty( $copy ) ? 0 : UPLOAD_ERR_NO_FILE;
                        $files[ $name.'[size]'.$levels ]     = !empty( $copy ) ? filesize( $path ) : 0;
                        $nf++;
                    }
                    else // POST data
                    {
                        $name = preg_replace( '/\[\]/', '['.$nd.']', $name );
                        $request[ $name ] = $value;
                        $nd++;
                    }
                }
                // finalize arrays
                $_REQUEST = array_merge( $_GET, $this->_data( $request ) );
                $_FILES   = $this->_data( $files );
                return true;
            }
        }
        return false;
    }

    /**
     * Parses a single line from the input string into an array
     */
    private function _line( $line='' )
    {
        $output = array();

        if( is_string( $line ) )
        {
            $line = preg_replace( '/^Content\-(Disposition|Type)\:[\ ]+/', 'content=', $line );
            $line = preg_replace( '/\;\s+/', '&', $line );
            $line = str_replace( '"', '', $line );
            parse_str( $line, $output );
        }
        return $output;
    }

    /**
     * Build final multi-level array from parsed data using parse_str
     */
    private function _data( $data=array() )
    {
        $output = array();

        if( is_array( $data ) )
        {
            $query = urldecode( http_build_query( $data, '', '&' ) );
            parse_str( $query, $output );
        }
        return $output;
    }

    /**
     * Returns a fallback value if a given non-numerical value is empty
     */
    private function _value( $value=null, $fallback='', $trim=false )
    {
        if( is_null( $value ) || $value === false || $value === '' )
        {
            return $fallback;
        }
        return $trim ? trim( $value ) : $value;
    }

}