<?php

class AuthorApi extends BaseAdmin {

    /**
     * 登录
     * @return array
     */
    public function index($params){

        $file=$_FILES['file']['tmp_name'];

        if(empty($file) || !is_file($file)) $this->error('文件不存在,请重新上传');

        $fp=fopen($file,'r');

        try{

            t('author')->start();

            while($line=fgets($fp)){

                if(empty(trim($line))) continue;

                list($author_id,$author_name,$avatar,$content_id,$url,$password,$content_type)=explode('&&&&&',trim($line));

                $author_id=trim($author_id);
                $author_name=trim($author_name);
                $avatar=trim($avatar);
                $content_id=trim($content_id);
                $url=trim($url);
                $password=trim($password);
                $content_type=trim($content_type);

                if(!preg_match('/^\d+$/',$author_id)) throw new Exception('作者id必须为整数', 1);
                if(!preg_match('/^\d+$/',$content_id)) throw new Exception('内容id必须为整数', 1);
                if(empty($author_name)) throw new Exception('作者名字不能为空', 1);

                $author=t('author')->where(['id'=>$author_id])->get();

                if(empty($author)) t('author')->insert(['id'=>$author_id,'name'=>$author_name,'avatar'=>$avatar]);

                $content_type=strtoupper($content_type);

                $type=0;
                if($content_type=='I') $type=1;
                elseif($content_type=='P') $type=2;
                // 添加内容
                t('content')->insert(['id'=>$content_id,'author_id'=>$author_id,'type'=>$type,'password'=>$password,'url'=>$url]);
            }

            t('author')->commit();
        }catch(Exception $e){

            t('author')->rollback();

            $this->error($e->getMessage(),$e->getCode());
        }

        fclose($fp);

        $this->ok();
    }
}