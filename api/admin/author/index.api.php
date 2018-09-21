<?php

/**
 * 作者管理
 */
class IndexApi extends BaseAdminAuth {

    public function index($params){

        $page=$params['page'];
        $limit=$params['limit'];

        $page<1 && $page=1;
        !$limit && $limit=20;

        $data=t('author')->where(['status'=>1])->limit([($page-1)*$limit,$limit])->find();

        $result=[];

        foreach ($data as $d) {
            
            $d['create_time']=date('Y-m-d H:i:s',$d['date_add']);

            $result[]=$d;
        }

        $total=t('author')->where(['status'=>1])->count();

        $this->ok(['authors'=>$result,'total'=>$total]);
    }

     public function detail($params){

        $author_id=$params['author_id'];

        if(empty($author_id)) $this->error('参数不全');

        $author=t('author')->field('*')->where(['id'=>$author_id])->get();

        if(empty($author)) $this->error("作者不存在");

        $author['create_time']=date('Y-m-d H:i:s',$author['date_add']);

        $this->ok($author);
    }

    public function add($params){

        $name=$params['name'];
        $avatar=$params['avatar'];

        if(empty($name) || empty($avatar)) $this->error("参数不全");

        t('author')->insert(['name'=>$name,'avatar'=>$avatar]);

        $this->ok();
    }

    public function edit($params){

        $author_id=$params['id'];
        $name=$params['name'];
        $avatar=$params['avatar'];

        if(empty($author_id) || empty($name) || empty($avatar)) $this->error("参数不全");

        t('author')->where(['id'=>$author_id])->update(['name'=>$name,'avatar'=>$avatar]);

        $this->ok();
    }

    public function del($params){

        $author_id=$params['uid'];

        if(empty($author_id)) $this->error('参数不全');

        t('user')->where(['id'=>$author_id])->update(['status'=>0]);

        $this->ok();
    }

    public function all($params){

        $authors=t('author')->field('id,name')->where(['status'=>1])->find();

        $this->ok($authors);
    }
}
