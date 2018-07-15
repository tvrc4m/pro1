<?php

/**
 * 
 */
class AuthorApi extends BaseAuth {

    /**
     * 购买过的可看的作者
     * @return array
     */
    public function index($params){

        $uid=$this->user['id'];
        $page=$params['page']??1;
        $limit=20;

        $last_time=strtotime('-30 day');
        // 30天有效期
        $bills=t('bill')->where(['user_id'=>$uid,'date_add'=>['$gt'=>$last_time]])->find();
        
        $authors=array_filter(array_unique(array_column($bills,'author_id')));
        
        if(empty($authors)) $this->ok();

        $authors=t('author')->where(['id'=>['$in'=>$authors]])->find();
        
        $this->ok($authors);
    }
}