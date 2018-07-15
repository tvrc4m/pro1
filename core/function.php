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