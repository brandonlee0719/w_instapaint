<?php

namespace Apps\PHPfox_AmazonS3\Model;

defined('PHPFOX') or exit ('NO DICE');

use Aws\S3\S3Client;
use Aws\Exception\AwsException;
use Phpfox;

/**
 * Class AmazonS3
 *
 * @author Neil J. <neil@phpfox.com>
 * @package Apps\PHPfox_AmazonS3\Model
 */
class AmazonS3
{
    /**
     * List all regions
     *
     * @var array
     */
    private $_aRegions = [
        'us-east-2' => 'US East (Ohio)',
        'us-east-1' => 'US East (N. Virginia)',
        'us-west-1' => 'US West (N. California)',
        'us-west-2' => 'US West (Oregon)',
        'ca-central-1' => 'Canada (Central)',
        'ap-south-1' => 'Asia Pacific (Mumbai)',
        'ap-northeast-2' => 'Asia Pacific (Seoul)',
        'ap-southeast-1' => 'Asia Pacific (Singapore)',
        'ap-southeast-2' => 'Asia Pacific (Sydney)',
        'ap-northeast-1' => 'Asia Pacific (Tokyo)',
        'eu-central-1' => 'EU (Frankfurt)',
        'eu-west-1' => 'EU (Ireland)',
        'eu-west-2' => 'EU (London)',
        'sa-east-1' => 'South America (São Paulo)',
    ];

    /**
     * AWS access key
     *
     * @var string
     */
    private $_sAwsKey = '';

    /**
     * AWS secret key
     *
     * @var string
     */
    private $_sAwsSecret = '';

    /**
     * Current bucket
     *
     * @var string
     */
    private $_sBucketName = '';

    /**
     * Current region
     *
     * @var string
     */
    private $_sCurrentRegion = '';

    /**
     * AmazonS3 constructor.
     * @param string $sAwsKey
     * @param string $sAwsSecret
     */
    public function __construct($sAwsKey = '', $sAwsSecret = '')
    {
        $this->_sAwsKey = $sAwsKey;
        $this->_sAwsSecret = $sAwsSecret;
        $this->_sBucketName = Phpfox::getParam('amazons3.cdn_bucket');
        $this->_sCurrentRegion = Phpfox::getParam('amazons3.cdn_region');
        if (empty($this->_sCurrentRegion) || $this->isValidRegion($this->_sCurrentRegion)) {
            $this->_sCurrentRegion = 'us-east-2';
        }
    }

    /**
     * Return all S3 regions
     *
     * @return array
     */
    public function getAllRegion()
    {
        return $this->_aRegions;
    }

    /**
     * Validation a key is valid
     *
     * @return bool
     */
    public function isValidKey()
    {
        $return = true;
        try {
            $oClient = new S3Client([
                'region' => $this->_sCurrentRegion,
                'version' => 'latest',
                'credentials' => [
                    'key' => $this->_sAwsKey,
                    'secret' => $this->_sAwsSecret
                ],
            ]);
            $oClient->listBuckets();
        } catch (AwsException $e) {
            $return = false;
        }
        return $return;
    }

    /**
     * List all buckets for current key
     *
     * @return array
     */
    public function getAllBucket()
    {
        $aAllBucket = [];
        if (!$this->_sAwsKey || !$this->_sAwsSecret) {
            return $aAllBucket;
        }
        $oClient = new S3Client([
            'region' => $this->_sCurrentRegion,
            'version' => 'latest',
            'credentials' => [
                'key' => $this->_sAwsKey,
                'secret' => $this->_sAwsSecret
            ],
        ]);
        $buckets = $oClient->listBuckets();
        foreach ($buckets['Buckets'] as $bucket) {
            $aAllBucket[] = [
                'name' => $bucket['Name'],
                'key' => md5($bucket['Name']),
                'in_use' => ($bucket['Name'] == $this->_sBucketName)
            ];
        }
        return $aAllBucket;
    }

    /**
     * Save a bucket
     *
     * @param string $sBucket
     */
    public function saveBucket($sBucket)
    {
        $oClient = new S3Client([
            'region' => $this->_sCurrentRegion,
            'version' => 'latest',
            'credentials' => [
                'key' => $this->_sAwsKey,
                'secret' => $this->_sAwsSecret
            ],
        ]);
        $sRegion = $oClient->determineBucketRegion($sBucket);

        Phpfox::getLib('database')->update(':setting', ['value_actual' => $sBucket],
            'var_name="cdn_bucket" AND module_id="amazons3"');
        Phpfox::getLib('database')->update(':setting', ['value_actual' => $sRegion],
            'var_name="cdn_region" AND module_id="amazons3"');
        Phpfox::getLib('cache')->remove();
    }

    /**
     * @param string $sBucket
     * @param string $sRegion
     * @return bool|string
     */
    public function createBucket($sBucket, $sRegion)
    {
        if (!$this->isValidRegion($sRegion)) {
            return false;
        }
        $status = true;
        $oClient = new S3Client([
            'region' => $sRegion,
            'version' => 'latest',
            'credentials' => [
                'key' => $this->_sAwsKey,
                'secret' => $this->_sAwsSecret
            ],
        ]);
        try {
            $oClient->createBucket([
                'Bucket' => $sBucket
            ]);
        } catch (AwsException $e) {
            $status = _p("Can not create bucket. Bucket name may already exist or invalid.");
        }
        return $status;
    }

    /**
     * Check a region is valid or not
     *
     * @param string $sRegion
     * @return bool
     */
    public function isValidRegion($sRegion)
    {
        return isset($this->_aRegions[$sRegion]) ? true : false;
    }
}
