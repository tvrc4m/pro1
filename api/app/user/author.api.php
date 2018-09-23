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

        $codes=t('code')->where(['user_id'=>$uid,'date_expired'=>['$gt'=>time()]])->find();

        if(empty($codes)) $this->ok();

        $authors=[];
        $author_expired=[];

        foreach ($codes as $code) {
            
            $author_codes=t('author_code')->where(['code_id'=>$code['id']])->find();

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

        $result=t('author')->where(['id'=>['$in'=>$authors]])->find();

        foreach ($result as &$res) {

            $res['expired_date']=$author_expired[$res['id']];
            $res['date_add']=date('Y-m-d H:i:s',$author['date_add']);
            
        }
         
        $this->ok($result);
    }
}