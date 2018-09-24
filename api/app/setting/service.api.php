<?php

class ServiceApi extends Base {

    /**
     * 首次加载
     * @return array
     */
    public function index($params){

        $author=$params['author'];

        $data=[];

        if(!empty($author)){

            $author=explode(',', $author);

            $author_id=$author[0];

            $author=t('author')->where(['id'=>$author_id])->get();

            $data['ad_img']=$author['ad_img'];
            $data['ad_redirect']=$author['ad_redirect'];
        }

        $setting=t('setting')->where(['name'=>'app_load_image'])->get();

        if(empty($setting)) $this->ok();

        // $data['']

        $this->ok(['images'=>explode(',', $setting['value'])]);
    }
}