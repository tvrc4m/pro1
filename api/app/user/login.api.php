<?php

class LoginApi extends Base {

    /**
     * 登录
     * @return array
     */
    public function index($params){

        $phone=$params['phone'];
        $password=$params['password'];

        $user=t('user')->where(['phone'=>$phone])->get();

        if(empty($user)) $this->error('该手机号不存在');

        if($user['password']!=$password) $this->error('密码错误');

        $token=sha1(base64_encode($phone.'='.time()));
        
        t('user')->where(['id'=>$user['id']])->update(['token'=>$token]);
        
        $this->ok(['token'=>$token]);
    }
}