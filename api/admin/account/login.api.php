<?php

/**
 * 后台账户登录
 */
class LoginApi extends BaseAdmin {

    public function index($params){

        $nickname=$params['nickname'];
        $password=$params['password'];

        if(empty($nickname) || empty($password)) $this->error('参数不全');

        $admin=t('admin')->where(['nickname'=>$nickname])->get();

        if(empty($admin)) $this->error('账户不存在');

        if(sha1($password)!=$admin['password']) $this->error('密码错误');

        $token=sha1(base64_encode($nickname.'='.time()));
        
        t('admin')->where(['id'=>$admin['id']])->update(['token'=>$token]);
        
        $this->ok(['token'=>$token]);
    }
}
