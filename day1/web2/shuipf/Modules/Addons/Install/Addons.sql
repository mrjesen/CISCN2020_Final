SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for `shuipfcms_addons`
-- ----------------------------
DROP TABLE IF EXISTS `shuipfcms_addons`;
CREATE TABLE `shuipfcms_addons` (
  `id` int(10) unsigned NOT NULL auto_increment COMMENT '主键',
  `name` varchar(40) NOT NULL COMMENT '插件名或标识，区分大小写',
  `title` varchar(20) NOT NULL COMMENT '中文名',
  `description` text COMMENT '插件描述',
  `status` tinyint(1) NOT NULL default '1' COMMENT '状态 1-启用 0-禁用 -1-损坏',
  `config` text COMMENT '配置 序列化存放',
  `author` varchar(40) default NULL COMMENT '作者',
  `version` varchar(20) default NULL COMMENT '版本号',
  `create_time` int(10) unsigned NOT NULL COMMENT '安装时间',
  `has_adminlist` tinyint(1) unsigned NOT NULL default '0' COMMENT '1-有后台列表 0-无后台列表',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='插件表';