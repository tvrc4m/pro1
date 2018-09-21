-- 作者
CREATE TABLE oa_author(
    id int AUTO_INCREMENT PRIMARY KEY,
    name varchar(32) NOT NULL COMMENT '作者名称',
    avatar varchar(256) COMMENT '头像地址',
    status int DEFAULT 1 COMMENT '作者状态',
    date_add int
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
-- 作者内容
CREATE TABLE oa_content(
    id int AUTO_INCREMENT PRIMARY KEY,
    author_id int NOT NULL COMMENT '作者id',
    type tinyint COMMENT '1:图文 2:视频',
    password varchar(64) COMMENT '内容密码',
    url varchar(521) DEFAULT '' COMMENT '图片链接', 
    date_add int
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 用户
CREATE TABLE oa_user(
    id int AUTO_INCREMENT PRIMARY KEY,
    phone varchar(11) COMMENT '手机号',
    password varchar(64) COMMENT '密码',
    token varchar(64) DEFAULT '' COMMENT '登录token',
    date_add int
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 订单
CREATE TABLE oa_bill(
    id int AUTO_INCREMENT PRIMARY KEY,
    author_id int NOT NULL COMMENT '作者id',
    user_id int NOT NULL,
    type tinyint DEFAULT 0 COMMENT '1:首次购买 2:续费 3:订阅',
    date_add int
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 用户查看过的内容
CREATE TABLE oa_user_view(
    id int AUTO_INCREMENT PRIMARY KEY,
    user_id int NOT NULL COMMENT '用户id',
    content_id int NOT NULL COMMENT '用户查看过的内容id',
    date_add int
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE oa_setting(
    id int AUTO_INCREMENT PRIMARY KEY,
    name varchar(32) NOT NULL COMMENT '设置键值',
    value varchar(1024) NOT NULL COMMENT '设置值',
    date_add int,
    UNIQUE INDEX name_index(name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 设备号
CREATE TABLE oa_device(
    id int AUTO_INCREMENT PRIMARY KEY,
    device_no varchar(64) NOT NULL,
    date_add int
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


INSERT INTO oa_device (device_no) values ('a76b55e1c7aa804');
INSERT INTO oa_setting (name,value) values ('app_load_image','http://img4.duitang.com/uploads/blog/201404/06/20140406232455_m5XVy.jpeg');

DROP TABLE IF EXISTS oa_code;

CREATE TABLE oa_code(
    id int AUTO_INCREMENT PRIMARY KEY,
    code varchar(16) NOT NULL COMMENT '订阅码',
    status tinyint DEFAULT 0 COMMENT '0:未使用 1:已使用 -1:已过期未使用',
    user_id int DEFAULT 0 COMMENT '关联的用户',
    year int COMMENT '年份,不足1年为0。按创建日期到过期日期算',
    month int COMMENT '月份',
    date_expired int COMMENT '过期日期',
    date_add int
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS oa_author_code;
-- 作者关联的邀请码
CREATE TABLE oa_author_code(
    id int AUTO_INCREMENT PRIMARY KEY,
    code_id varchar(16) NOT NULL COMMENT '订阅码',
    author_id int NOT NULL DEFAULT 0 COMMENT '作者id',
    date_add int
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE oa_app_device(
   
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- CREATE TABLE oa_

CREATE TABLE oa_admin(
    id int AUTO_INCREMENT PRIMARY KEY,
    nickname varchar(11) COMMENT '昵称',
    password varchar(64) COMMENT '密码',
    token varchar(64) DEFAULT '' COMMENT '登录token',
    date_add int
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


