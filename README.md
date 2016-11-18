# Yii 2 Amazon S3 Component

Add to your configuration

```php
'components' => [
    // ...
    's3' => [
        'class' => \indielab\yii2s3\S3::class,
        'bucket' => 'mybucket',
        'key' => 'KEY',
        'secret' => 'SECRET',
        'region' => 'eu-central-1',
    ],
    // ...
]
```

Using the component in order to upload a file:

```php
Yii::$app->s3->upload('path/to/the/file.jpg');
```

Where file.jpg will be used as the key of the uploading file. Now in order to get the url to a key use:

```php
$url = Yii::$app->s3->url('file.jpg');
```