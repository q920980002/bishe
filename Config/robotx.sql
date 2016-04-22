DROP table if exists `user`;
CREATE TABLE `user` (
`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`username` varchar(100) NOT NULL DEFAULT '' COMMENT '用户名',
`password` varchar(64) NOT NULL DEFAULT '' COMMENT '密码',
`nickname` varchar(64) NOT NULL DEFAULT '' COMMENT '昵称',
`phone` bigint(11) unsigned NOT NULL DEFAULT '0' COMMENT '注册手机号',
`sex` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '性别 0:未知 1:男 2:女',
`age` int(4) unsigned NOT NULL DEFAULT '0' COMMENT '年龄',
`status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '账号状态：1正常，0锁定，－1注销',
`create_time` int(11) NOT NULL DEFAULT '0' COMMENT '账号创建时间',
`update_time` int(11) NOT NULL DEFAULT '0' COMMENT '用户信息最后更新时间，更新前需入快照表',
PRIMARY KEY (`id`),
UNIQUE KEY `uk_user_name` (`username`),
UNIQUE KEY `uk_user_phone` (`phone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户表';


DROP table if exists `body_fat_data`;
CREATE TABLE `body_fat_data` (
`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`u_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
`username` varchar(100) NOT NULL DEFAULT '' COMMENT '用户名',
`procedure_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '程序号',
`sex` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '性别 0:未知 1:男 2:女',
`age` int(4) unsigned NOT NULL DEFAULT '0' COMMENT '年龄',
`weight` float(10,2) unsigned NOT NULL DEFAULT '0' COMMENT '体重',
`fat` float(5,2) unsigned NOT NULL DEFAULT '0' COMMENT '脂肪率',
`muscle` float(5,2) unsigned NOT NULL DEFAULT '0' COMMENT '肌肉率',
`water` float(5,2) unsigned NOT NULL DEFAULT '0' COMMENT '水份率',
`bone` varchar(100) NOT NULL DEFAULT '' COMMENT '骨骼',
`bmi` float(5,2) unsigned NOT NULL DEFAULT '0' COMMENT 'bmi',
`kcal` float(5,2) unsigned NOT NULL DEFAULT '0' COMMENT '基本能力代谢',
`create_time` int(11) NOT NULL DEFAULT '0' COMMENT '数据创建时间',
`update_time` int(11) NOT NULL DEFAULT '0' COMMENT '数据信息最后更新时间，更新前需入快照表',
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='人体脂肪秤';



DROP table if exists `body_general_data`;
CREATE TABLE `body_general_data` (
`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`u_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
`username` varchar(100) NOT NULL DEFAULT '' COMMENT '用户名',
`procedure_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '程序号',
`sex` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '性别 0:未知 1:男 2:女',
`age` int(4) unsigned NOT NULL DEFAULT '0' COMMENT '年龄',
`weight` float(10,2) unsigned NOT NULL DEFAULT '0' COMMENT '体重',
`bmi` float(5,2) unsigned NOT NULL DEFAULT '0' COMMENT 'bmi',
`create_time` int(11) NOT NULL DEFAULT '0' COMMENT '数据创建时间',
`update_time` int(11) NOT NULL DEFAULT '0' COMMENT '数据信息最后更新时间，更新前需入快照表',
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='人体普通秤';


DROP table if exists `body_vegetable_data`;
CREATE TABLE `body_vegetable_data` (
`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`u_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
`username` varchar(100) NOT NULL DEFAULT '' COMMENT '用户名',
`procedure_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '程序号',
`type` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '菜品种类',
`weight` float(10,2) unsigned NOT NULL DEFAULT '0' COMMENT '体重',
`create_time` int(11) NOT NULL DEFAULT '0' COMMENT '数据创建时间',
`update_time` int(11) NOT NULL DEFAULT '0' COMMENT '数据信息最后更新时间，更新前需入快照表',
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='厨房秤';


