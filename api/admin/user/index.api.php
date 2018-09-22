<?php

/**
 * 前台账户
 */
class IndexApi extends BaseAdminAuth {

    public function index($params){

        $page=$params['page'];
        $limit=$params['limit'];

        $page<1 && $page=1;
        !$limit && $limit=20;

        $data=t('user')->field('id,phone,date_add')->limit([($page-1)*$limit,$limit])->find();

        $result=[];

        foreach ($data as $d) {
            
            $d['create_time']=date('Y-m-d H:i:s',$d['date_add']);

            $result[]=$d;
        }

        $total=t('user')->count();

        $this->ok(['users'=>$result,'total'=>$total]);
    }

    public function detail($params){

        $uid=$params['uid'];

        if(empty($uid)) $this->error('参数不全');

        $user=t('user')->field('id,phone,date_add')->where(['id'=>$uid])->get();

        if(empty($user)) $this->error("用户不存在");

        $user['create_time']=date('Y-m-d H:i:s',$user['date_add']);

        $this->ok($user);
    }

    public function add($params){

        $phone=$params['phone'];
        $password=$params['password'];

        if(empty($phone) || empty($password)) $this->error("参数不全");

        $user=t('user')->where(['phone'=>$phone])->get();

        if(!empty($user)) $this->error("该用户已存在");

        t('user')->insert(['phone'=>$phone,'password'=>sha1($password)]);

        $this->ok();
    }

    public function edit($params){

        $uid=$params['id'];
        $phone=$params['phone'];
        $password=$params['password'];

        if(empty($uid) || empty($phone)) $this->error("参数不全");

        $user=t('user')->where(['id'=>$uid])->get();

        if(empty($user)) $this->error("该用户不存在");

        $set=['phone'=>$phone];

        !empty($password) && $set['password']=sha1($password);

        t('user')->where(['id'=>$uid])->update($set);

        $this->ok();
    }

    public function del($params){

        $user_id=$params['uid'];

        if(empty($user_id)) $this->error('参数不全');

        t('user')->delete(['id'=>$user_id]);

        $this->ok();
    }

    public function search($params){

        $search=$params['search'];

        if(empty($search)) $this->ok([]);

        $users=t('user')->field('id,phone')->where(['phone'=>['$like'=>$search]])->limit(20)->find();

        $this->ok($users);
    }
    /**
     * 用户订阅的作者
     * @param  array $params 
     * @return 
     */
    public function author($params){

        $user_id=$params['user_id'];
        $page=$params['page'];
        $limit=20;
        $page<1 && $page=1;

        if(empty($user_id)) $this->ok('未指定用户');
        
        $result=t('author_code')->field('author_id,GROUP_CONCAT(code_id) as codes')->where(['user_id'=>$user_id])->limit([($page-1)*$limit,$limit])->group('author_id')->find();

        if($result){

            $author_ids=array_column($result, 'author_id');

            $authors=t('author')->where(['id'=>['$in'=>$author_ids],'status'=>1])->find();

            foreach ($authors as &$author) {

                $author['create_time']=date('Y-m-d',$author['date_add']);
                
                foreach ($result as $res) {
                    
                    if($author['id']==$res['author_id']){

                        $codes=explode(',', $res['codes']);

                        foreach ($codes as $code_id) {
                            
                            $code=t('code')->where(['id'=>$code_id])->get();

                            $code['expired_time']=date('Y-m-d',$code['date_expired']);

                            $author['codes'][]=$code;
                        }
                    }
                }
            }
        }

        $total=t('author_code')->where(['user_id'=>$user_id])->group('author_id')->count();

        $this->ok(['authors'=>$authors,'total'=>$total]);
    }
}
