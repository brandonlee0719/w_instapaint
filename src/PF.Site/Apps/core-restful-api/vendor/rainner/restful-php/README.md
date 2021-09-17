[demo]: http://demo.sudofolio.com/parallaxor/example/
[twitter]: http://twitter.com/raintek_
[mit]: http://www.opensource.org/licenses/mit-license.php
[request]: http://php.net/manual/en/reserved.variables.request.php

# PHP Restful Package

This is a small package for working with HTTP input data in PHP. When a POST request comes into PHP, the Content-Type and Content-Body (Raw input) of the request are handled and parsed into the _POST/_FILES arrays. This package parses the raw input body for all RESTful verbs (POST, PUT, DELETE, PATCH, etc.) into the _REQUEST/_FILES arrays and provides a better way for working with uploaded files.

### HTTP Input Parser Usage

The Parser class will do the same thing PHP already does for GET/POST data, but for all other REQUEST_METHOD verbs and put the data into the [$_REQUEST][request] and $_FILES super globals *(Without $_COOKIE)*.

This class looks at the **Content-Type** of the request to decide how it will parse the raw content body of the request. As long as the request is formatted properly it should work without any issues. Currently it is capable of parsing the following content types:

* `text/plain` as INI data.
* `text/html` as HTML formatted data.
* `application/json` as JSON encoded data.
* `application/x-www-form-urlencoded` as URL encoded data.
* `application/xml` as XML formatted data.
* `multipart/form-data` as Multipart form data.

Multi-dimentional array type property names (`multi[level][name]`) are also supported and parsed into regular array by this class.

```php
// parse incoming request data
$request = new Restful\Parser();
$request->parse();

// preview
echo '<pre>'.print_r( $_REQUEST, true ).'</pre>' . "\n";
echo '<pre>'.print_r( $_FILES, true ).'</pre>' . "\n";
exit;
```

**Restful\Parser** public methods:

| Name               | Args        | Return    | Description                                      |
| ------------------ | ----------- | --------- | ------------------------------------------------ |
| `resolveMethod()`  | `none`      | `null`    | Resolve request method verb                      |
| `resolveInput()`   | `none`      | `null`    | Resolve content-type and other input data        |
| `setContentType()` | `$ctype`    | `null`    | Set the content-type to use                      |
| `setBoundary()`    | `$boundary` | `null`    | Set the form-data boundary string to use         |
| `setRawInput()`    | `$input`    | `null`    | Set the raw input string to parse                |
| `parse()`          | `none`      | `boolean` | Parse the input data for different content types |

### HTTP Files Manager

The Files class will take the $_FILES array and re-format the structure to make it much easier for working with multiple file uploads and nested multi-dimentional array keys, for example:

```html
<form>
    <input type="file" name="files[avatar]" accept="image/*" />
    <input type="file" name="files[photos][]" accept="image/*" multiple />
</form>
```

The form above would have two file dialogue buttons, one accepting a single file, and another accepting multiple files. Here is how we would access these files using the Files class:

```php
// import and format files from the $_FILES array
$files = new Restful\Files();
$files->parse();

// work on the avatar image
$files->loopFiles( 'files.avatar', function( $file )
{
    // save to db, process, etc...
    echo $file['tmp_name'] ." <br /> \n";
});

// move photos to a folder
$photos = $files->moveFiles( 'files.photos', '/path/to/uploads' );
foreach( $photos as $photo )
{
    // check each file for upload/move errors
    echo !empty( $photo['error'] ) ? $photo['error'] : 'Success';
    echo "<br /> \n";
}
```

**Restful\Files** public methods:

| Name           | Args                        | Retuen   | Description                                           |
| -------------- | --------------------------- | -------- | ----------------------------------------------------- |
| `blacklist()`  | `$ext1, $ext2, ...`         | `null`   | Set a list of file extensions to skip when moving     |
| `parse()`      | `none`                      | `null`   | Parses a given FILES array                            |
| `getParsed()`  | `none`                      | `null`   | Returns the new parsed files array structure          |
| `getFiles()`   | `$key`                      | `array`  | Returns a list of files for passed string key         |
| `moveFiles()`  | `$key, $dirname, $organize` | `array`  | Move list of files to a folder for given key          |
| `loopFiles()`  | `$key, $callback`           | `null`   | Loops over list of files and calls a function on each |

### Installation &amp; Setup

**Manual:** Clone this repo somewhere in your project and use the included autoloader to load the included classes:

````php
$ cd /path/to/project/libs
$ git clone https://github.com/rainner/restful-php.git
````

````php
<?php
require( './libs/restful-php/autoloader.php' )
````

**Composer:** Run the following composer commands to include this package and install the dependency for your project:

````php
$ composer require rainner/restful-php 1.*
````

### Author

Rainner Lins: [@raintek_][twitter]

### License

Licensed under [MIT][mit].


