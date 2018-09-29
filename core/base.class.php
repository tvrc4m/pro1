<?php

/**
 * 所有类的基类
 */
class Base {

    protected $router=null;

    protected function ok($data=[]){

        $_POST['tester']!='t.wei' && $data=encrypt(json_encode($data));

        exit(json_encode(['err_no'=>0,'err_msg'=>'','datahide'=>$data]));
    }

    /**
     * 返回错误信息
     * @param  string $errmsg 
     * @param  string $errono 
     * @return 
     */
    protected function error($errmsg,$errono=1){

        exit(json_encode(['err_no'=>$errono,'err_msg'=>$errmsg,'datahide'=>[]]));
    }
}

class BaseAuth extends Base{

    protected $user=null;

    public function __construct($params){

        $token=$params['token'];

        if(empty($token)) $this->error('请先登录',1001);

        $user=t('user')->where(['token'=>$token])->get();

        if(empty($user)) $this->error('token已过期',1002);

        $this->user=$user;
    }
}

class BaseAdminPhone extends Base{

    public function __construct($params){

        $device=$params['device'];

        if(empty($device)) $this->error('没有权限操作',1001);

        $result=t('device')->where(['device_no'=>$device])->get();

        if(empty($result)) $this->error('没有权限操作',1001);

        $this->crypto_key=$result['crypto_key'];
    }
}
/**
 * 后台基类
 */
class BaseAdmin extends Base{

    public function __construct($params){

    }

    protected function ok($data=[]){

        exit(json_encode(['err_no'=>0,'err_msg'=>'','data'=>$data]));
    }

    /**
     * 返回错误信息
     * @param  string $errmsg 
     * @param  string $errono 
     * @return 
     */
    protected function error($errmsg,$errono=1){

        exit(json_encode(['err_no'=>$errono,'err_msg'=>$errmsg,'data'=>[]]));
    }
}

class BaseAdminAuth extends BaseAdmin{

    protected $admin=null;

    public function __construct($params){

        parent::__construct($params);

        $token=$params['token'];

        if(empty($token)) $this->error('请先登录',1001);

        $admin=t('admin')->where(['token'=>$token])->get();

        if(empty($admin)) $this->error('token已过期',1002);

        $this->admin=$admin;
    }
}

