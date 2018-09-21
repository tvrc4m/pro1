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
}