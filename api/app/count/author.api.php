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

        // 30天有效期
        $bills=t('bill')->field(['author_id,count(1) as count'])->group(['author_id'])->limit([0,$limit])->find();

        $data=[];

        foreach ($bills as $bill) {
            
            $author=t('author')->where(['id'=>$bill['author_id']])->get();

            $data[]=['name'=>$author['name'],'avatar'=>$author['avatar'],'count'=>$bill['count']];
        }

        $this->ok($data);
    }
}