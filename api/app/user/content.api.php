<?php

/**
 * 
 */
class ContentApi extends BaseAuth {
    // 每个作者的限定上限
    private $limit=10;

    private $ignore=[13521092668];

    /**
     * 用户购买的内容列表
     * @return array
     */
    public function index($params){

        $uid=$this->user['id'];
        $page=$params['page']??1;
        $author=strtolower($params['author']);

        if(in_array($this->user['phone'],$this->ignore)){
            return $this->get_contents();
        }

        if(empty($author)) $this->ok();

        if($author!='all') $where=['author_id'=>['$in'=>explode(',', $author)]]; 

        $codes=t('code')->where(['user_id'=>$uid,'date_expired'=>['$gt'=>time()]])->find();

        if(empty($codes)) $this->ok();

        $authors=[];
        $author_expired=[];

        foreach ($codes as $code) {

            $where['code_id']=$code['id'];
            
            $author_codes=t('author_code')->where($where)->find();

            foreach ($author_codes as $author_code) {
                
                $authors[]=$author_code['author_id'];

                $date_expired=$code['date_expired'];

                $expired_time=$code['date_expired']-time();

                if($expired_time<60){
                    $left='剩余'.$expired_time.'秒';
                }else if($expired_time<3600){
                    $left='剩余'.ceil($expired_time/60).'分钟';
                }elseif($expired_time<86400*3){
                    $left='剩余'.ceil($expired_time/86400).'天';
                }else{
                    $left=date('Y-m-d',$code['date_expired']);
                }

                $author_expired[$author_code['author_id']]=$left;
            }

        }

        $authors=array_unique($authors);
        
        if(empty($authors)) $this->ok();

        $this->move_history($authors);

        $views=t('user_view')->where(['user_id'=>$uid,'author_id'=>['$in'=>$authors]])->find();

        $content_ids=array_filter(array_unique(array_column($views,'content_id')));

        $where=['author_id'=>['$in'=>$authors],'date_pub'=>['$lt'=>time()]];
        
        if(!empty($content_ids)) $where['id']=['$nin'=>$content_ids];
        
        $contents=t('content')->where($where)->sort('id DESC')->find();
        
        foreach ($contents as $index=>$content) {
            // 记录已经阅读过
            t('user_view')->insert(['user_id'=>$uid,'author_id'=>$content['author_id'],'content_id'=>$content['id']]);

            $author=t('author')->where(['id'=>$content['author_id']])->get();

            $contents[$index]['author_name']=$author['name'];
            $contents[$index]['author_avatar']=$author['avatar'];
            $contents[$index]['expired_date']=$author_expired[$content['author_id']];

            $contents[$index]['date_add']=date('Y-m-d H:i:s',$content['date_add']);
        }
  
        $this->ok($contents);
    }

    private function move_history($authors){
        
        foreach ($authors as $author_id) {
            
            $return=t('content')->field('id')->where(['author_id'=>$author_id,'date_pub'=>['$lt'=>time()]])->sort('id DESC')->limit($this->limit)->find();

            if(empty($return)) continue;

            $content_ids=array_column($return, 'id');
            
            $contents=t('content')->where(['author_id'=>$author_id,'date_pub'=>['$lt'=>time()],'id'=>['$nin'=>$content_ids]])->find();
            
            if(empty($contents)) continue;

            $waiting_delete=[];

            foreach ($contents as $content) {

                $waiting_delete[]=$content['id'];
                
                t('content_history')->insert([
                    'content_id'=>$content['id'],
                    'author_id'=>$content['author_id'],
                    'type'=>$content['type'],
                    'password'=>$content['password'],
                    'url'=>$content['url'],
                    'description'=>$content['description'],
                    'date_pub'=>$content['date_pub']
                ]);
            }
            
            t('content')->delete(['id'=>['$in'=>$waiting_delete]]);
        }
    }

    private function get_contents(){

        $contents=t('content')->where([])->sort('id DESC')->find();
        
        foreach ($contents as $index=>$content) {
            
            $author=t('author')->where(['id'=>$content['author_id']])->get();

            $contents[$index]['author_name']=$author['name'];
            $contents[$index]['author_avatar']=$author['avatar'];
            $contents[$index]['expired_date']='2050-12-12';
            $contents[$index]['date_add']=date('Y-m-d H:i:s',$content['date_add']);
        }
  
        $this->ok($contents);
    }
}