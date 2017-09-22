CREATE TABLE `sign_student` (
  `id` int(11) NOT NULL,
  `room` int(11) NOT NULL DEFAULT '0' COMMENT '教室号',
  `lat` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0' COMMENT '纬度',
  `long` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0' COMMENT '经度',
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL COMMENT '姓名',
  `number` bigint(20) NOT NULL COMMENT '学号',
  `status` int(11) NOT NULL DEFAULT '0' COMMENT '状态(0/已签到;1/请假)',
  `reason` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '请假理由',
  `time` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '签到时间'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC;

CREATE TABLE `sign_teacher` (
  `id` int(11) NOT NULL,
  `room` int(11) NOT NULL DEFAULT '0' COMMENT '教室号',
  `lat` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0' COMMENT '纬度',
  `long` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0' COMMENT '经度',
  `status` int(11) NOT NULL DEFAULT '0' COMMENT '状态(0/开始签到;1/结束签到)'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC;

ALTER TABLE `sign_student`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `sign_teacher`
  ADD PRIMARY KEY (`id`);
