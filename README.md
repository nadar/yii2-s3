# Yii 2 Amazon S3 Component

This component allows you to work with the amazon AWS S3 buckets for uploading and finding files.

## Installation

Add the the package to your composer file:

```sh
composer require indielab/yii2s3
```

Add the component to your application configuration file:

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

## Usage

Using the component in order to upload a file:

```php
Yii::$app->s3->upload('path/to/the/file.jpg');
```

Where file.jpg will be used as the key of the uploading file. Now in order to get the url to a key use:

```php
$url = Yii::$app->s3->url('file.jpg');
```

### Configure Uploading

You can also provide more options to the uploading configuration method:

```php
Yii::$app->s3->upload('path/to/the/file.jpg', [
    'override' => true, // whether existing file should be overriden or not
    'Key' => 'CacheControlTestFile.txt', // Define a specific name for the file instead of the source file name
    'CacheControl' => 'max-age=' . strtotime('+1 year')  // Add cache controler options
]);
```