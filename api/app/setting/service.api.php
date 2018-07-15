<?php

class ServiceApi extends Base {

    /**
     * 首次加载
     * @return array
     */
    public function index($params){

        $setting=t('setting')->where(['name'=>'app_load_image'])->get();

        if(empty($setting)) $this->ok();

        $this->ok(['images'=>explode(',', $setting['value'])]);
    }
}