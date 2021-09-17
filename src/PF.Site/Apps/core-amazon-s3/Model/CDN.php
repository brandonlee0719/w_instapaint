<?php

namespace Apps\PHPfox_AmazonS3\Model;


use Aws\S3\S3Client;
use Phpfox;
use Aws\S3\Exception\S3Exception;
use Aws\Exception\AwsException;

if (!defined('CURL_SSLVERSION_TLSv1')) {
    define('CURL_SSLVERSION_TLSv1', 'TLSv1.x');
}

/**
 * PHP class that extends the PHPfox CDN core.
 * We use
 *
 * @package Apps\PHPfox_AmazonS3\Model
 */
class CDN extends \Core\CDN
{

    private static $_oS3Client;

    private $_bucket;

    private $_region = '';

    public function __construct()
    {
        $this->_region = Phpfox::getParam('amazons3.cdn_region');
        $this->_bucket = Phpfox::getParam('amazons3.cdn_bucket');

        if (self::$_oS3Client == null) {
            $start = microtime(true);
            self::$_oS3Client = new S3Client([
                'region' => $this->_region,
                'version' => 'latest',
                'credentials' => [
                    'key' => setting('cdn_amazon_id'),
                    'secret' => setting('cdn_amazon_secret'),
                ],
            ]);
            $end =  microtime(true);

            Phpfox::getLog('test.log')->info('initialized '. ($end - $start));
        }
    }

    public function getUrl($path)
    {
        $key = str_replace(\Phpfox::getParam('core.path_file'), '', $path);
        return self::$_oS3Client->getObjectUrl($this->_bucket, $key);
    }

    public function put($file, $name = '')
    {
        $start = microtime(true);
        if (empty($name)) {
            $name = str_replace("\\", '/', str_replace(PHPFOX_DIR, '', $file));
        }
        $bStatus = self::$_oS3Client->putObject([
            'Bucket' => $this->_bucket,
            'Key' => $name,
            'SourceFile' => $file,
            'ACL' => 'public-read',
        ]);

        if (\Phpfox::getParam('core.keep_files_in_server') == false) {
            register_shutdown_function(function () use ($file) {
                @unlink($file);
            });
        }

        $end =  microtime(true);

        Phpfox::getLog('test.log')->info('put ' . ($end - $start));

        return $bStatus;
    }

    public function remove($file)
    {
        $start =  microtime(true);

        $key = str_replace("\\", '/', str_replace(PHPFOX_DIR, '', $file));

        $result = self::$_oS3Client->deleteObject([
            'Bucket' => $this->_bucket,
            'Key' => $key
        ]);

        $end =  microtime(true);

        Phpfox::getLog('test.log')->info('removed ' . ($end - $start));

        return $result;
    }

    public function getServerId()
    {
        if (!setting('cdn_enabled')) {
            return 0;
        }

        return 1;
    }

    public function __returnObject()
    {
        return $this;
    }
}
