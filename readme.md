## upload

[![Build Status](https://travis-ci.org/zhuayi/upload.svg)](https://travis-ci.org/zhuayi/upload)
[![Total Downloads](https://poser.pugx.org/zhuayi/upload/d/total.svg)](https://packagist.org/packages/zhuayi/upload)
[![Latest Stable Version](https://poser.pugx.org/zhuayi/upload/v/stable.svg)](https://packagist.org/packages/zhuayi/upload)
[![Latest Unstable Version](https://poser.pugx.org/zhuayi/upload/v/unstable.svg)](https://packagist.org/packages/zhuayi/upload)
[![License](https://poser.pugx.org/zhuayi/upload/license.svg)](https://packagist.org/packages/zhuayi/upload)


## Installation

#### New Project installation

####In order to install Laravel 5 Zhuayi, just add

```php
"zhuayi/upload": "@stable"
```
to your composer.json. Then run composer install or composer update.

Then in your config/app.php add

```php
Zhuayi\admin\UploadServiceProvider::class
```

Run Publish
```shell
php artisan vendor:publish --force
```

modify **config/upload.php**   

```php
return [

    'default' => 'local',
    
    'local' => [
        'path' => public_path() . '/uploads',
        'url' => '/uploads',
    ],

    'qiniu' => [
        'accessKey' => accessKey,
        'secretKey' => secretKey,
        'bucket' => bucket,
        'path' => path,
        'url' => url,
    ]
];
```


##Usage

```php
$reset = Zhuayi\upload\upload::file('Filedata');

if (is_null($reset->error)) {

    return $reset->url;

} else {

    return $reset->error;
}
```