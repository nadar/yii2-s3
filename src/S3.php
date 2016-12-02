<?php

namespace indielab\yii2s3;

use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use Aws\S3\S3Client;

/**
 * S3 Amazon Component.
 * 
 * @property \Aws\S3\S3Client $client The s3 client.
 * @author Basil Suter <basil@nadar.io>
 */
class S3 extends Component
{
    /**
     * @var string The region e.g `eu-central-1` which is EU Frankfurt.
     */
    public $region = null;
    
    /**
     * @var string The name of the bucket.
     */
    public $bucket = null;
    
    /**
     * @var string The aws key.
     */
    public $key = null;
    
    /**
     * @var string The aws secret.
     */
    public $secret = null;
    
    /**
     * @var string Access contorl: Valid values: private|public-read|public-read-write|authenticated-read|aws-exec-read|bucket-owner-read|bucket-owner-full-control
     */
    public $acl = 'public-read';
    
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        
        if ($this->region === null || $this->bucket === null || $this->key === null) {
            throw new InvalidConfigException("region, bucket and key must be provided for s3 component configuration.");
        }
    }
    
    private $_client = null;

    /**
     * Get the Amazon client library.
     * 
     * @return \Aws\S3\S3Client
     */
    public function getClient()
    {
        if ($this->_client === null) {
            $this->_client = new S3Client(['version' => 'latest', 'region' => $this->region, 'credentials' => ['key' => $this->key, 'secret' => $this->secret]]);
        }
        
        return $this->_client;
    }
    
    /**
     * Upload a file source to the Bucket.
     * 
     * @param string $filePath
     * @param array $options You can provide options to the putObject method of the S3Client.
     * - override: Wehther to check if the file exists or not.
     * - Key: The file name which will be used as identifier on the storage system.
     * - CacheControl: Define a max-age ability like `max-age=172800`
     * @return boolean|\Aws\Result
     */
    public function upload($filePath, array $options = [])
    {
        $override = ArrayHelper::remove($options, 'override', false);
        $key = ArrayHelper::remove($options, 'Key', pathinfo($filePath, PATHINFO_BASENAME));
        
        if (!$override && $this->find($key)) {
            return false;
        }
        
        $configure = ArrayHelper::merge([
            'ACL' => $this->acl,
            'Bucket' => $this->bucket,
            'Key' => $key,
            'SourceFile' => $filePath,
        ], $options);
        
        $put = $this->client->putObject($configure);
        
        if ($put) {
            return $put['ObjectURL'];
        }
        
        return false;
    }
    
    /**
     * Get the url from a key file based on the bucket and region.
     * 
     * @param string $key The key file.
     * @return string
     */
    public function url($key)
    {
        return $this->client->getObjectUrl($this->bucket, $key);
    }
    
    /**
     * See whether a key exists or not.
     * 
     * @param string $key
     * @return \Aws\Result|boolean
     */
    public function find($key)
    {
        try {
            return $this->client->getObject(['Bucket' => $this->bucket, 'Key' => $key]);
        } catch (\Aws\S3\Exception\S3Exception $e) {
            return false;
        }
    }
}