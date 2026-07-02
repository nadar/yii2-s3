# Upgrade Instructions

This file contains upgrade notes for the `indielab/yii2s3` component. Read through the notes for the version you are
upgrading *to*, working your way up from the version you are currently on.

## Upgrade from 2.x to 3.0

Version 3.0 raises the minimum PHP requirement. There are **no changes to the public API** — the `S3` component and all
of its methods behave exactly as in 2.x, so no application code changes are required beyond meeting the new PHP version.

### Dropped PHP 8.2 and 8.3 support

The minimum supported PHP version is now **8.4**. Supported versions are **8.4** and **8.5**.

The Composer constraint changed from `^8.2` to `^8.4`:

```diff
 "require": {
-    "php": "^8.2",
+    "php": "^8.4",
     "aws/aws-sdk-php": "^3.0",
     "yiisoft/yii2": "^2.0"
 }
```

### Raised aws/aws-sdk-php floor

The `aws/aws-sdk-php` requirement was raised from `^3.0` to `^3.387`:

```diff
 "require": {
     "php": "^8.4",
-    "aws/aws-sdk-php": "^3.0",
+    "aws/aws-sdk-php": "^3.387",
     "yiisoft/yii2": "^2.0"
 }
```

This only raises the minimum; the SDK stays within the `3.x` line. `composer update` will pull the latest compatible
release.

### How to upgrade

1. Make sure your runtime and CI run on PHP 8.4 or 8.5.
2. Update the requirement in your `composer.json`:

   ```sh
   composer require indielab/yii2s3:^3.0
   ```

3. Run `composer update indielab/yii2s3` and deploy.

If you cannot move to PHP 8.4 yet, stay on the `2.x` release line, which continues to support PHP 8.2 and 8.3.
