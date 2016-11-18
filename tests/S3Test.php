<?php

namespace indielab\yii2s3\tests;

use PHPUnit\Framework\TestCase;
use indielab\yii2s3\S3;

class S3Test extends TestCase
{
    public function setUp()
    {
        require(__DIR__ . '/../vendor/yiisoft/yii2/Yii.php');
    }
    
    public function testPutObject()
    {
        $client = new S3(['bucket' => BUCKET, 'key' => KEY, 'secret' => SECRET, 'region' => 'eu-central-1']);
        
        $this->assertNotFalse($client->upload(__DIR__ . '/testfile.txt', true));
        
        $this->assertNotFalse($client->find('testfile.txt'));
        
        $this->assertNotFalse($client->url('testfile.txt'));
        
    }
}