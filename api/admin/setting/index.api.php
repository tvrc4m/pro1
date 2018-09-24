<?php

/**
 * 前台账户
 */
class IndexApi extends BaseAdminAuth {

    public function index($params){

    }

    public function get(){

        $setting=t('setting')->find();

        $this->ok($setting);
    }

    public function save($params){

        foreach ($params['settings'] as $setting) {
            
            $name=$setting['name'];
            $value=$setting['value'];

            t('setting')->where(['name'=>$name])->update(['value'=>$value]);
        }

        $this->ok();
    }
}
