<?php

/**
 * 
 */
class UserApi extends BaseAuth {

    /**
     * 购买过的可看的作者
     * @return array
     */
    public function index($params){

        $uid=$this->user['id'];
        $page=$params['page']??1;
        $limit=20;

        // 订阅用户统计
        $bills=t('bill')->field(['user_id,count(1) as count'])->where(['type'=>3])->group(['type'])->limit([0,$limit])->find();

        $data=[];

        foreach ($bills as $bill) {
            
            $author=t('user')->where(['id'=>$author_id])->get();

            $data[]=['name'=>$author['name'],'avatar'=>$author['avatar'],'count'=>$bill['count']];
        }

        $this->ok($data);
    }
}