<?php

/**
 * 
 */
class StatisticApi extends BaseAuth {

    /**
     * 购买过的可看的作者
     * @return array
     */
    public function index($params){

        $date_add=$this->user['date_add'];

        $register_day=ceil((time()-$date_add)/3600*24);

        $subscribe=t('author_code')->field('count(DISTINCT author_id) as count')->get();

        $data=['register_day'=>$register_day,'subscribe_count'=>$subscribe['count']];
        
        $this->ok($data);
    }
}