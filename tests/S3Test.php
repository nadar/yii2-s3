<?php

namespace indielab\yii2s3\tests;

use PHPUnit\Framework\TestCase;
use indielab\yii2s3\S3;

class S3Test extends TestCase
{   
    public function testPutObject()
    {
        require(__DIR__ . '/../vendor/yiisoft/yii2/Yii.php');
        
        $client = new S3(['bucket' => BUCKET, 'key' => KEY, 'secret' => SECRET, 'region' => REGION]);
        
        $this->assertNotFalse($client->upload(__DIR__ . '/testfile.txt', ['override' => true]));
        
        $this->assertNotFalse($client->find('testfile.txt'));
        
        $this->assertNotFalse($client->url('testfile.txt'));
        
    }
    
    public function testPutObjectWithCacheController()
    {
        
        $client = new S3(['bucket' => BUCKET, 'key' => KEY, 'secret' => SECRET, 'region' => REGION]);
    
        $age = strtotime('+1 year');
        
        $this->assertNotFalse($client->upload(__DIR__ . '/testfile.txt', ['override' => true, 'Key' => 'CacheControlTestFile.txt', 'CacheControl' => 'max-age=' . $age ]));
    
        $bucket = $client->find('CacheControlTestFile.txt');
        
        $this->assertSame($bucket['@metadata']['headers']['cache-control'], 'max-age='.$age);
        
    }
}