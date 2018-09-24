<?php

class IndexApi extends BaseAdminAuth{

    public function index($params){

        $author_id=$params['author_id'];
        $page=$params['page'];
        $limit=$params['limit'];

        $page<1 && $page=1;
        !$limit && $limit=20;

        $where=[];

        !empty($author_id) && $where['author_id']=$author_id;

        $contents=t('content')->where($where)->limit([($page-1)*$limit,$limit])->find();

        foreach ($contents as &$content) {
            
            $author=$this->_get_author($content['author_id']);

            $content['author_name']=$author['name'];

            $content['create_time']=date('Y-m-d',$content['date_add']);
            $content['pub_time']=date('Y-m-d',$content['date_pub']);
        }

        $total=t('content')->where($where)->count();

        $this->ok(['contents'=>$contents,'total'=>$total]);
    }

    public function detail($params){

        $content_id=$params['content_id'];

        if(empty($content_id)) $this->error('参数不全');

        $content=t('content')->where(['id'=>$content_id])->get();

        if(empty($content)) $this->error('内容不存在');

        $content['pub_time']=date('Y-m-d H:i:s',$content['date_pub']);

        $this->ok($content);
    }

    public function add($params){

        $author_id=$params['author_id'];
        $type=$params['type'];
        $password=$params['password'];
        $url=$params['url'];
        $pub_time=$params['pub_time'];

        empty($pub_time) && $pub_time=date('Y-m-d H:i:s');

        if(empty($author_id) || empty($type) || empty($url)) $this->error("参数不全");

        $content=t('content')->where(['url'=>$url])->get();

        if(!empty($content)) $this->error("该内容已存在");

        t('content')->insert(['author_id'=>$author_id,'type'=>$type,'url'=>$url,'password'=>$password,'date_pub'=>strtotime($pub_time)]);

        $this->ok();
    }

    public function edit($params){

        $content_id=$params['id'];
        $author_id=$params['author_id'];
        $type=$params['type'];
        $password=$params['password'];
        $url=$params['url'];
        $pub_time=$params['pub_time'];

        empty($pub_time) && $pub_time=date('Y-m-d H:i:s');

        if(empty($content_id) || empty($author_id) || empty($type) || empty($url)) $this->error("参数不全");

        $content=t('content')->where(['id'=>$content_id])->get();

        if(empty($content)) $this->error("该内容不存在");

        t('content')->where(['id'=>$content_id])->update(['author_id'=>$author_id,'type'=>$type,'url'=>$url,'password'=>$password,'date_pub'=>strtotime($pub_time)]);

        $this->ok();
    }

    public function del($params){

        $content_id=$params['content_id'];

        if(empty($content_id)) $this->error('参数不全');

        t('content')->delete(['id'=>$content_id]);

        $this->ok();
    }

    public function multiadd($params){

        $contents=$params['contents'];

        if(empty($contents)) $this->error('内容为空');

        try{

            t('content')->start();

            foreach ($contents as $content) {
            
                $author_id=$content['author_id'];
                $type=$content['type'];
                $password=$content['password'];
                $url=$content['url'];
                $pub_time=$params['pub_time'];

                empty($pub_time) && $pub_time=date('Y-m-d H:i:s');

                if(empty($author_id) || empty($type) || empty($url)) throw new Exception('参数不全', 1);

                $content=t('content')->where(['url'=>$url])->get();

                if(!empty($content)) throw new Exception($url.'该内容已存在', 1);

                t('content')->insert(['author_id'=>$author_id,'type'=>$type,'url'=>$url,'password'=>$password,'date_pub'=>strtotime($pub_time)]);
            }

            t('content')->commit();
        }catch(Exception $e){

            t('content')->rollback();

            $this->error($e->getMessage());
        }

        $this->ok([]);
    }

    public function _get_author($author_id){

        static $authors=[];

        if($authors[$author_id]) return $authors[$author_id];

        $authors[$author_id]=t('author')->where(['id'=>$author_id])->get();

        return $authors[$author_id];
    }
}