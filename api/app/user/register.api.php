<?php

class RegisterApi extends Base {

    /**
     * 登录
     * @return array
     */
    public function index($params){

        $phone=$params['phone'];
        $password=$params['password'];
        $code=$params['code'];
        $author=$params['author'];

        if(empty($author)) $this->error('不允许注册');

        $code=t('code')->where(['code'=>$code])->get();

        if(empty($code)) $this->error('邀请码不存在');

        if($code['user_id']) $this->error('邀请码已使用');

        if($code['date_expired']<time()) $this->error('邀请码已过期');

        $user=t('user')->where(['phone'=>$phone])->get();

        if(!empty($user)) $this->error('用户已注册');

        $has_role=false;

        if($author!='all'){

            $author_code=t('author_code')->where(['code_id'=>$code['id']])->find();

            $authors=array_column($author_code,'author_id');

            $roles=explode(',', $author);

            foreach ($roles as $role) {
                
                if(in_array($role, $authors)){

                    $has_role=true;

                    break;
                }
            }
        }else{
            $has_role=true;
        }

        if(!$has_role) $this->error('不允许注册');
        
        $token=sha1(base64_encode($phone.'='.time()));

        try{

            t('user')->start();

            $uid=t('user')->insert(['phone'=>$phone,'password'=>sha1($password),'token'=>$token]);

            t('code')->where(['id'=>$code['id']])->update(['user_id'=>$uid]);

            t('author_code')->where(['code_id'=>$code['id']])->update(['user_id'=>$uid]);

            t('user')->commit();
            
        }catch(Exception $e){

            t('user')->rollback();

            $this->error('注册失败');
        }
                
        $this->ok(['token'=>$token]);
    }
}