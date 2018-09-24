<?php

class UploadApi extends BaseAdminAuth{

    public function avatar(){

        $image=$_FILES['file']['tmp_name'];

        if(!file_exists($image)) $this->error('头像不存在');

        $ext=pathinfo($image,PATHINFO_EXTENSION);

        if(empty($ext) && strpos($image,'.')){

            $ext=end(explode('.', $image));
        }

        $path='/static/img/avatar/';

        $filename=uniqid().($ext?'.'.$ext:'');
        
        $status=move_uploaded_file($image, ROOT.$path.$filename);

        if(!$status) $this->error('上传失败');

        $this->ok(['url'=>$path.$filename]);
    }

    /**
     * 上传文件
     * @return
     */
    public function content($params){

        $file=$_FILES['file']['tmp_name'];

        if(!file_exists($file)) $this->error('文件不存在');

        $ext=pathinfo($_FILES['file']['name'],PATHINFO_EXTENSION);

        if($ext!='txt') $this->error('只能上传txt文件');

        $content=@file_get_contents($file);

        if(empty($content)) $this->ok('文件为空');

        try{

            t('content')->start();

            $fp=fopen($file,'r');

            while ($content=fgets($fp)) {

                if(empty($content)) continue;
                
                @list($author_id,$type,$url,$password,$date_pub)=explode('##', $content);

                $author=t('author')->where(['id'=>$author_id])->get();

                if(empty($author)) throw new Exception('作者'.$author_id.'不存在', 1);

                if(!in_array($type, [1,2])) throw new Exception('有不支持的内容类型', 1);

                if(empty($url)) throw new Exception('有内容链接为空', 1);

                empty($date_pub) && $date_pub=date('Y-m-d H:i:s');

                t('content')->insert(['author_id'=>$author_id,'type'=>$type,'password'=>$password,'url'=>$url,'date_pub'=>strtotime($date_pub)]);
            }

            t('content')->commit();
        }catch(Exception $e){

            t('content')->rollback();

            $this->error($e->getMessage());
        }

        $this->ok();        
    }
}