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

    // 配置文件
    private $config;
    
    //上传地址前缀
    private $prefix;

    public function __construct($config)
    {

        $this->config = $config;
        if ($config == 'qiniu') {

            $this->auth = new QiniuAuth(Config('upload')['qiniu']['accessKey'], Config('upload')['qiniu']['secretKey']);
            $this->token = $this->auth->uploadToken(Config('upload')['qiniu']['bucket']);
            $this->uploadMgr = new UploadManager();
        }
        
        $this->path = Config('upload')[$config]['path'];
        $this->$prefix = Config('upload')[$config]['url'];

        if (substr($this->url, strlen($this->url) - 1) == "/") {

            $this->url = substr($this->url, 0, strlen($this->url) - 1);
        }
    }

    public static function with($config = '') {
        
        if (is_null(self::$object)) {
            self::$object = new self($config);
        }
        return self::$object;
    }


    public static function file($fileName = '', $newName = '',$fromHttp = true) {

        if (is_null(self::$object)) {
            self::$object = new self(Config('upload')['default']);
        }

        self::$object->file = $fromHttp?Input::file($fileName):$fileName;

        if (empty($newName)) {
            self::$object->newName = $newName = self::$object->path.'/'.uniqid() ."." . self::$object->file->getClientOriginalExtension();
        } else {

            self::$object->newName = $newName = self::$object->path.'/'. $newName;
        }

        if (self::$object->config == 'local') {
            
           return self::$object->uploadToLocal();

        } else if (self::$object->config == 'qiniu') {

            return self::$object->uploadToQiniu();
        }

        
    }
    
    public function getToken(){
        return self::$object->token;
    }

    private function uploadToQiniu() {

        list($ret, $err) = self::$object->uploadMgr->putFile(self::$object->token, self::$object->newName, self::$object->file->getRealPath());
        if (is_null($err)) {
            self::$object->url = self::$object->$prefix."/".$ret['key'];

        } else {
            self::$object->error = $err->message();
        }

        return self::$object;
    }
    
 

    private function uploadToLocal() {
        if (!is_dir(dirname(self::$object->newName))) {
            mkdir(dirname(self::$object->newName), 0777, true);
        }

        self::$object->file->move(dirname(self::$object->newName), basename(self::$object->newName));

        self::$object->url = str_replace(self::$object->path, self::$object->url, self::$object->newName);

        return self::$object;
    }
     

}