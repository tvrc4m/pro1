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

        foreach ($authors as $index=>$author) {

            $bill=t('bill')->where(['user_id'=>$uid,'author_id'=>$author['id']])->sort('id DESC')->get();
            
            $expired_time=strtotime('+30 days',$bill['date_add'])-$bill['date_add'];
            
            if($expired_time<60){
                $authors[$index]['expired_date']='剩余'.$expired_time.'秒';
            }else if($expired_time<3600){
                $authors[$index]['expired_date']='剩余'.ceil($expired_time/60).'分钟';
            }elseif($expired_time<86400*3){
                $authors[$index]['expired_date']='剩余'.ceil($expired_time/86400).'天';
            }else{
                $authors[$index]['expired_date']=date('Y-m-d',strtotime('+30 days',$author['date_add']));
            }

            $authors[$index]['date_add']=date('Y-m-d H:i:s',$author['date_add']);
        }
        
        $this->ok($authors);
    }
}