<?php

namespace indielab\yii2s3;

use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;
use GuzzleHttp\Psr7\Stream;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;

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
    public function init(): void
    {
        parent::init();

        if ($this->region === null || $this->bucket === null || $this->key === null) {
            throw new InvalidConfigException("region, bucket and key must be provided for s3 component configuration.");
        }
    }

    private ?S3Client $_client = null;

    /**
     * Get the Amazon client library.
     *
     * @return \Aws\S3\S3Client
     */
    public function getClient()
    {
        if ($this->_client === null) {
            $this->_client = new S3Client([
                'version' => 'latest',
                'region' => $this->region,
                'credentials' => ['key' => $this->key, 'secret' => $this->secret]
            ]);
            $this->_client->registerStreamWrapper();
        }

        return $this->_client;
    }

    /**
     * Upload a file source to the Bucket.
     *
     * @param array<String> $options You can provide options to the putObject method of the S3Client.
     * - override: Wehther to check if the file exists or not.
     * - Key: The file name which will be used as identifier on the storage system.
     * - CacheControl: Define a max-age ability like `max-age=172800`
     */
    public function upload(string $filePath, array $options = []): false|string
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

        try {
            $put = $this->client->putObject($configure);

            $objectUrl = $put['ObjectURL'] ?? false;

            if ($objectUrl) {
                return (string) $objectUrl;
            }

        } catch (S3Exception) {

        }

        return false;
    }

    /**
     * Get the url from a key file based on the bucket and region.
     */
    public function url(string $key): string
    {
        return $this->client->getObjectUrl($this->bucket, $key);
    }

    /**
     * See whether a key exists or not.
     * @return \Aws\Result<array>|false
     */
    public function find(string $key): \Aws\Result|false
    {
        try {
            return $this->client->getObject(['Bucket' => $this->bucket, 'Key' => $key]);
        } catch (S3Exception) {
            return false;
        }
    }

    public function findObjectStream(string $key): false|Stream
    {
        $o = $this->find($key);

        if ($o) {
            return $o['Body'];
        }

        return false;
    }

    public function findObjectContent(string $key): false|string
    {
        $stream = $this->getStream($key);

        if ($stream) {
            return $stream->getContents();
        }

        return false;
    }

    /**
     * Returns a stream for the given file
     *
     * @param string $key The file name like "xyz.jpg"
     * @return resource|false
     */
    public function fileSystemStream(string $key)
    {
        return fopen("s3://{$this->bucket}/{$key}", "r");
    }

    /**
     * Delete a given file
     */
    public function delete(string $fileName): bool
    {
        return (bool) $this->client->deleteObject([
            'Bucket' => $this->bucket,
            'Key' => $fileName,
        ]);
    }

    /**
     * Returns the file size in bytes or false if the file does not exist or an error eccured.
     */
    public function fileSize(string $fileKey): int|false
    {
        try {
            $result = $this->client->headObject([
                'Bucket' => $this->bucket,
                'Key' => $fileKey,
            ]);

            // Return the size of the file
            $bytes = $result['ContentLength'] ?? false; // Size in bytes

            if ($bytes) {
                return (int) $bytes;
            }
        } catch (S3Exception) {
        }

        return false;
    }
}
