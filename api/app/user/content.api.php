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

        if(!empty($content_ids)) $where['id']=['$nin'=>$content_ids];

        $limit=[($page-1)*$limit,$limit];

        $contents=t('content')->where($where)->sort('id DESC')->limit($limit)->find();

        foreach ($contents as $content) {
            // 记录已经阅读过
            t('user_view')->insert(['user_id'=>$uid,'content_id'=>$content['id']]);
        }

        $this->ok($contents);
    }
}