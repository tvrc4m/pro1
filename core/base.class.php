<?php

/**
 * 所有类的基类
 */
class Base {

    protected function ok($data=[]){

        $data=encrypt(json_encode($data));

        exit(json_encode(['err_no'=>0,'err_msg'=>'','data'=>$data]));
    }

    /**
     * 返回错误信息
     * @param  string $errmsg 
     * @param  string $errono 
     * @return 
     */
    protected function error($errmsg,$errono=1){

        exit(json_encode(['errono'=>$errono,'errmsg'=>$errmsg,'data'=>[]]));
    }
}

class BaseAuth extends Base{

    protected $user=null;

    public function __construct(){

        $token=$_REQUEST['token'];

        if(empty($token)) $this->error('请先登录',1001);

        $user=t('user')->where(['token'=>$token])->get();

        if(empty($user)) $this->error('token已过期',1002);

        $this->user=$user;
    }
}

class BaseAdmin extends Base{

    public function __construct(){

        $device=$_REQUEST['device'];

        if(empty($device)) $this->error('没有权限操作',1001);

        $result=t('device')->where(['device_no'=>$device])->get();

        if(empty($result)) $this->error('没有权限操作',1001);

        $this->crypto_key=$result['crypto_key'];
    }
}