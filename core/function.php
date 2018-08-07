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

    return @openssl_encrypt($str,CRYPT_CIPHER,CRYPT_KEY, 0);
}

function decrypt($str){

    return @openssl_decrypt($str,CRYPT_CIPHER,CRYPT_KEY, 0);
}


define('CRYPT_KEY','5b6953f1d4c64');
define('CRYPT_CIPHER','AES-128-CBC');

$str= encrypt(json_encode(['phone'=>'15763951212','password'=>'83656adb4c9caafec3ec0c84223fafec457adaba']));
$str='dsJMAoQA\/Lm+aZ7305YjL2NIpjSqXAoMeam1ljuzYp7hGsy7QzNikOCSfmDvuoDcbFkRBszDwJAUTAYalAC9bQ==';
$str="";
var_dump(json_decode($str,true));exit;
// echo $str;
$result=decrypt($str);
echo $result;
var_dump($result);
// print_r(json_decode($result,true));