<?php

/**
 * 初始化db实现并指定表
 * @param  string $table 表名(省略表前缀)
 * @return object
 */
function t($table){
    // 初始化，单例模式
    $db=DB::init();
    // 指定表名
    $db->setTable($table);

    return $db;
}

function encrypt($str){

    return base64_encode(@openssl_encrypt($str,CRYPT_CIPHER,CRYPT_KEY, 1,CRTYP_IV));
}

function decrypt($str){

    return @openssl_decrypt($str,CRYPT_CIPHER,CRYPT_KEY, 0,CRTYP_IV);
}

// define('CRYPT_KEY','com.secret.hidepng.superman');
// define('CRTYP_IV', '01234567');
// define('CRYPT_CIPHER','des-ede3-cbc');

// // $str= encrypt("111111");
// // $str='dsJMAoQA\/Lm+aZ7305YjL2NIpjSqXAoMeam1ljuzYp7hGsy7QzNikOCSfmDvuoDcbFkRBszDwJAUTAYalAC9bQ==';
// $str="b+y1bC3Cu2ikHP8nFsUgm/9I4UpSQ23YZuIrpqFg+EnRGVv+LVBA6lUZisHh JzmA";
// // var_dump(json_decode($str,true));exit;
// // echo $str;
// $result=decrypt($str);
// // echo $result;
// var_dump($result);
// print_r(json_decode($result,true));