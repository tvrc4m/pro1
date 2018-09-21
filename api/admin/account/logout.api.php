<?php

/**
 * 后台账户退出登录
 */
class LogoutApi extends BaseAdminAuth {

    public function index(){

        t('admin')->where(['id'=>$this->admin['id']])->update(['token'=>'']);

        $this->ok();
    }
}
