-- ----------------------------
-- Table structure for `shuipfcms_search`
-- ----------------------------
DROP TABLE IF EXISTS `shuipfcms_search`;
CREATE TABLE `shuipfcms_search` (
  `searchid` int(10) unsigned NOT NULL auto_increment,
  `id` mediumint(8) unsigned NOT NULL default '0' COMMENT '信息id',
  `catid` smallint(5) unsigned NOT NULL default '0' COMMENT '栏目id',
  `modelid` smallint(5) default NULL COMMENT '模型id',
  `adddate` int(10) unsigned NOT NULL COMMENT '添加时间',
  `data` text NOT NULL COMMENT '数据',
  PRIMARY KEY  (`searchid`),
  KEY `id` USING BTREE (`id`,`catid`,`adddate`),
  KEY `modelid` (`modelid`,`catid`),
  FULLTEXT KEY `data` (`data`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='全站搜索数据表';

DROP TABLE IF EXISTS `shuipfcms_search_keyword`;
CREATE TABLE `shuipfcms_search_keyword` (
  `keyword` char(20) NOT NULL,
  `pinyin` char(20) NOT NULL,
  `searchnums` int(10) unsigned NOT NULL,
  `data` char(20) NOT NULL,
  UNIQUE KEY `keyword` (`keyword`),
  UNIQUE KEY `pinyin` (`pinyin`),
  FULLTEXT KEY `data` (`data`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='搜索关键字表';