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

            $authors=explode(',', $author);

            $author_id=min($authors);

            $author=t('author')->where(['id'=>$author_id])->get();

            $data['author']['ad_img']=$author['ad_img'];
            $data['author']['ad_redirect']=$author['ad_redirect'];
        }

        $setting_ad_img=t('setting')->where(['name'=>'ad_img'])->get();
        $setting_ad_redirect=t('setting')->where(['name'=>'ad_redirect'])->get();

        $data['site']['ad_img']=$setting_ad_img['value'];
        $data['site']['ad_redirect']=$setting_ad_redirect['value'];


        $this->ok($data);
    }
}