<?php
// +----------------------------------------------------------------------
// |  [ 我的梦想是星辰大海 ]
// +----------------------------------------------------------------------
// | Author: yc  yc@yuanxu.top
// +----------------------------------------------------------------------
// | Date: 17.7.1 Time: 14:54
// +----------------------------------------------------------------------
$sql = "
DROP TABLE IF EXISTS `ims_yc_expressage_api`;
CREATE TABLE `ims_yc_expressage_api` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0' COMMENT '公众号id',
  `uid` int(11) DEFAULT NULL COMMENT '管理员id',
  `EBusinessID` varchar(255) DEFAULT '' COMMENT '快递api ID',
  `key` varchar(255) DEFAULT '' COMMENT '快递api KEY',
  `template_id` varchar(255) DEFAULT NULL COMMENT '微信信息通知ID',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_EBusinessID` (`EBusinessID`),
  KEY `idx_key` (`key`),
  KEY `idx_template` (`template_id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ims_yc_expressage_user
-- ----------------------------
DROP TABLE IF EXISTS `ims_yc_expressage_user`;
CREATE TABLE `ims_yc_expressage_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `uniacid` int(11) DEFAULT NULL COMMENT '公众号id',
  `openid` varchar(255) DEFAULT NULL COMMENT '用户openid',
  `kname` varchar(255) DEFAULT NULL COMMENT '快递名称',
  `kid` varchar(255) DEFAULT NULL COMMENT '快递编号',
  `kcode` varchar(255) DEFAULT NULL COMMENT '快递代码',
  `state` varchar(255) DEFAULT NULL COMMENT '快递状态',
  `content` text COMMENT '快递内容',
  `createtime` varchar(255) DEFAULT NULL COMMENT '查询时间',
  `update_time` varchar(255) DEFAULT NULL,
  `wait_notification` int(1) DEFAULT '0' COMMENT '等待通知',
  `is_subscribe` int(1) DEFAULT '0' COMMENT '是否订阅',
  PRIMARY KEY (`id`),
  KEY `index_uniacid` (`uniacid`),
  KEY `index_openid` (`openid`),
  KEY `index_kname` (`kname`),
  KEY `index_kid` (`kid`),
  KEY `index_status` (`state`),
  KEY `index_createtime` (`createtime`),
  KEY `index_kcode` (`kcode`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

";

pdo_query($sql);