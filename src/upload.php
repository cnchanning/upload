<?php

namespace Zhuayi\upload;

use Input;
use Qiniu\Auth as QiniuAuth;
use Qiniu\Storage\UploadManager;

class upload {

    private static  $object;

    // Input::file 获取的对象
    private $file;

    // 七牛的auth验证
    private $auth;

    // 七牛的auth验证
    private $token;

    // 七牛的上传对象
    private $uploadMgr;

    // 上传路径, 从 config 里取
    public $path;

    // 新名称, 如果为空则取uniqid()
    public $newName;

    // 上传成功后的url
    public $url;

    // 上传失败后的error
    public $error;

    public function __construct()
    {
        $this->auth = new QiniuAuth(Config('upload')['accessKey'], Config('upload')['secretKey']);
        $this->token = $this->auth->uploadToken(Config('upload')['bucket']);
        $this->uploadMgr = new UploadManager();
        $this->path = Config('upload')['path'];
        $this->url = Config('upload')['url'];
       
        if (substr($this->url, strlen($this->url) - 1) == "/") {

            $this->url = substr($this->url, 0, strlen($this->url) - 1);
        }
    }

    public static function file($fileName = '', $newName = '') {

        if (is_null(self::$object)) {
            self::$object = new self;
        }
        
        $file = self::$object->file = Input::file($fileName);

        if (empty($newName)) {
            self::$object->newName = $newName = self::$object->path.'/'.uniqid() ."." . $file->getClientOriginalExtension();
        } else {

            self::$object->newName = $newName = self::$object->path.'/'. $newName;
        }

        list($ret, $err) = self::$object->uploadMgr->putFile(self::$object->token, $newName, $file->getRealPath());
        if (is_null($err)) {
            self::$object->url = self::$object->url."/".$ret['key'];

        } else {
            self::$object->error = $err->message();
        }

        return self::$object;
        
    }


}