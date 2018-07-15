<?php

class UserApi extends BaseAdmin {

    /**
     * 登录
     * @return array
     */
    public function index($params){

        $file=$_FILES['file']['tmp_name'];
        
        if(empty($file) || !is_file($file)) $this->error('文件不存在,请重新上传');

        $fp=fopen($file,'r');

        try{
            
            t('user')->start();

            while($line=fgets($fp)){

                if(empty(trim($line))) continue;

                list($phone,$password,$author_id,$type)=explode('&&&&&',trim($line));

                $author_id=trim($author_id);
                $password=trim($password);
                $phone=trim($phone);
                $type=trim($type);

                if(!preg_match('/^\d+$/',$author_id)) throw new Exception('作者id必须为整数:'.$author_id, 1);
                
                if(!preg_match('/^\d{11}$/',$phone)) throw new Exception('用户手机号不正确:'.$phone, 1);

                if(strtoupper($type)=='N' && empty($password)) throw new Exception('新增用户没有设置密码', 1);
                
                $author=t('author')->where(['id'=>$author_id])->get();

                if(empty($author)) throw new Exception('作者id为'.$author_id."不存在", 1);

                $user=t('user')->where(['phone'=>$phone])->get();

                if(empty($user)) $user_id=t('user')->insert(['phone'=>$phone,'password'=>sha1($password)]);

                else $user_id=$user['id'];

                switch (strtoupper($type)) {
                    case 'N':$bill_type=1;break;
                    case 'X':$bill_type=2;break;
                    case 'Z':$bill_type=3;break;
                    default:$bill_type=0;break;
                }
                
                t('bill')->insert(['author_id'=>$author_id,'user_id'=>$user_id,'type'=>$bill_type]);
            }

            t('user')->commit();
        }catch(Exception $e){

            t('user')->rollback();

            $this->error($e->getMessage(),$e->getCode());
        }

        fclose($fp);

        $this->ok();
    }
}