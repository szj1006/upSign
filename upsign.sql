CREATE TABLE IF NOT EXISTS `sign_class` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `number` bigint(20) NOT NULL COMMENT '学号',
  `name` varchar(50) NOT NULL COMMENT '姓名',
  `class` varchar(50) NOT NULL COMMENT '班级',
  PRIMARY KEY (`id`),
  UNIQUE KEY `number` (`number`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `sign_student` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `room` int(11) NOT NULL COMMENT '教室号',
  `lat` varchar(50) NOT NULL COMMENT '纬度',
  `long` varchar(50) NOT NULL COMMENT '经度',
  `name` varchar(50) NOT NULL COMMENT '姓名',
  `number` bigint(20) NOT NULL COMMENT '学号',
  `status` int(11) NOT NULL COMMENT '状态(0/已签到;1/请假)',
  `reason` varchar(50) DEFAULT NULL COMMENT '请假理由',
  `time` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '签到时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

CREATE TABLE IF NOT EXISTS `sign_teacher` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `room` int(11) NOT NULL COMMENT '教室号',
  `lat` varchar(50) NOT NULL COMMENT '纬度',
  `long` varchar(50) NOT NULL COMMENT '经度',
  `class` varchar(50) NOT NULL COMMENT '班级',
  `status` int(11) NOT NULL COMMENT '状态(0/开始签到;1/结束签到)',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;