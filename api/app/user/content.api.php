<?php

/**
 * 
 */
class ContentApi extends BaseAuth {

    /**
     * 用户购买的内容列表
     * @return array
     */
    public function index($params){

        $uid=$this->user['id'];
        $page=$params['page']??1;
        $limit=20;

        $last_time=strtotime('-30 day');
        // 30天有效期
        $bills=t('bill')->field('author_id')->where(['user_id'=>$uid,'date_add'=>['$gt'=>$last_time]])->find();

        $authors=array_filter(array_unique(array_column($bills,'author_id')));
        
        if(empty($authors)) $this->ok();

        $views=t('user_view')->where(['user_id'=>$uid])->find();

        $content_ids=array_filter(array_unique(array_column($views,'content_id')));

        $where=['author_id'=>['$in'=>$authors]];

        // if(!empty($content_ids)) $where['id']=' NOT IN ('.implode(',', $content_id).')';

        $limit=[($page-1)*$limit,$limit];

        $contents=t('content')->where($where)->sort('id DESC')->limit($limit)->find();

        foreach ($contents as $index=>$content) {
            // 记录已经阅读过
            t('user_view')->insert(['user_id'=>$uid,'content_id'=>$content['id']]);

            $author=t('author')->where(['id'=>$content['author_id']])->get();

            $contents[$index]['author_name']=$author['name'];
            $contents[$index]['author_avatar']=$author['avatar'];
            
            $expired_time=strtotime('+30 days',$content['date_add'])-$content['date_add'];
            if($expired_time<60){
                $contents[$index]['expired_date']='剩余'.$expired_time.'秒';
            }else if($expired_time<3600){
                $contents[$index]['expired_date']='剩余'.ceil($expired_time/60).'分钟';
            }elseif($expired_time<86400*3){
                $contents[$index]['expired_date']='剩余'.ceil($expired_time/86400).'天';
            }else{
                $contents[$index]['expired_date']=date('Y-m-d',strtotime('+30 days',$content['date_add']));
            }

            $contents[$index]['date_add']=date('Y-m-d H:i:s',$content['date_add']);
        }
  
        $this->ok($contents);
    }
}