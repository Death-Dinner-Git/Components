/*
Navicat MySQL Data Transfer

Source Server         : 127.0.0.1
Source Server Version : 50617
Source Host           : 127.0.0.1:3306
Source Database       : admin

Target Server Type    : MYSQL
Target Server Version : 50617
File Encoding         : 65001

Date: 2017-01-09 10:25:14
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `lxl_admin`
-- ----------------------------
DROP TABLE IF EXISTS `lxl_admin`;
CREATE TABLE `lxl_admin` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL DEFAULT '' COMMENT '帐号',
  `password` varchar(32) NOT NULL DEFAULT '',
  `token` varchar(10) NOT NULL DEFAULT '',
  `last_login_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '登录时间',
  `last_login_ip` varchar(40) NOT NULL DEFAULT '' COMMENT '登录ip',
  `addtime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
  `updatetime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '最近更新时间',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of lxl_admin
-- ----------------------------
INSERT INTO `lxl_admin` VALUES ('1', 'admin', '71c8e9cae8f160cc618f72e958c2c38a', 'wCAPtQuX', '1483923590', '127.0.0.1', '1442626898', '0', '1');
INSERT INTO `lxl_admin` VALUES ('2', '', '', '', '1483923641', '127.0.0.1', '0', '0', '0');

-- ----------------------------
-- Table structure for `lxl_admin_login_log`
-- ----------------------------
DROP TABLE IF EXISTS `lxl_admin_login_log`;
CREATE TABLE `lxl_admin_login_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '状态',
  `inputtime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '时间',
  `username` varchar(50) NOT NULL DEFAULT '' COMMENT '帐号',
  `admin_id` int(11) unsigned NOT NULL DEFAULT '0',
  `ip` varchar(50) NOT NULL DEFAULT '' COMMENT 'IP地址',
  `login_type` varchar(255) NOT NULL DEFAULT '',
  `note` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='管理登录记录';

-- ----------------------------
-- Records of lxl_admin_login_log
-- ----------------------------
INSERT INTO `lxl_admin_login_log` VALUES ('1', '1', '1483600500', 'admin', '1', '127.0.0.1', '', '登录成功');
INSERT INTO `lxl_admin_login_log` VALUES ('2', '1', '1483664200', 'admin', '1', '127.0.0.1', '', '登录成功');
INSERT INTO `lxl_admin_login_log` VALUES ('3', '1', '1483665412', 'admin', '1', '127.0.0.1', '', '登录成功');
INSERT INTO `lxl_admin_login_log` VALUES ('4', '1', '1483923590', 'admin', '1', '127.0.0.1', '', '登录成功');
INSERT INTO `lxl_admin_login_log` VALUES ('5', '1', '1483923641', 'admin', '1', '127.0.0.1', '', '登录成功');

-- ----------------------------
-- Table structure for `lxl_auth_group`
-- ----------------------------
DROP TABLE IF EXISTS `lxl_auth_group`;
CREATE TABLE `lxl_auth_group` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `title` char(100) NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `rules` varchar(255) NOT NULL DEFAULT '',
  `addtime` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='用户组表';

-- ----------------------------
-- Records of lxl_auth_group
-- ----------------------------
INSERT INTO `lxl_auth_group` VALUES ('1', '编辑', '1', '13,14', '1483603415');
INSERT INTO `lxl_auth_group` VALUES ('2', '运营', '1', '13', '1483604519');

-- ----------------------------
-- Table structure for `lxl_auth_group_access`
-- ----------------------------
DROP TABLE IF EXISTS `lxl_auth_group_access`;
CREATE TABLE `lxl_auth_group_access` (
  `uid` mediumint(8) unsigned NOT NULL,
  `group_id` mediumint(8) unsigned NOT NULL,
  UNIQUE KEY `uid_group_id` (`uid`,`group_id`),
  KEY `uid` (`uid`),
  KEY `group_id` (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户组明细表';

-- ----------------------------
-- Records of lxl_auth_group_access
-- ----------------------------
INSERT INTO `lxl_auth_group_access` VALUES ('2', '1');
INSERT INTO `lxl_auth_group_access` VALUES ('11', '2');

-- ----------------------------
-- Table structure for `lxl_auth_rule`
-- ----------------------------
DROP TABLE IF EXISTS `lxl_auth_rule`;
CREATE TABLE `lxl_auth_rule` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(80) NOT NULL DEFAULT '',
  `title` char(20) NOT NULL DEFAULT '',
  `type` tinyint(1) NOT NULL DEFAULT '1',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `condition` char(100) NOT NULL DEFAULT '',
  `pid` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '父ID',
  `addtime` int(11) unsigned NOT NULL DEFAULT '0',
  `classid` smallint(5) unsigned DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COMMENT='规则表';

-- ----------------------------
-- Records of lxl_auth_rule
-- ----------------------------
INSERT INTO `lxl_auth_rule` VALUES ('1', 'Admin/index', '管理员列表', '1', '1', '', '0', '1480577105', '1');
INSERT INTO `lxl_auth_rule` VALUES ('2', 'Admin/addadmin', '添加管理员', '1', '1', '', '0', '1480577129', '1');
INSERT INTO `lxl_auth_rule` VALUES ('3', 'Admin/editadmin', '修改管理员', '1', '1', '', '0', '1480577155', '1');
INSERT INTO `lxl_auth_rule` VALUES ('4', 'Admin/deladmin', '删除管理员', '1', '1', '', '0', '1480577180', '1');
INSERT INTO `lxl_auth_rule` VALUES ('5', 'Admin/auth_group', '角色管理', '1', '1', '', '0', '1480577200', '1');
INSERT INTO `lxl_auth_rule` VALUES ('6', 'Admin/group_add', '添加角色', '1', '1', '', '0', '1480577222', '1');
INSERT INTO `lxl_auth_rule` VALUES ('7', 'Admin/group_edit', '编辑角色', '1', '1', '', '0', '1480577243', '1');
INSERT INTO `lxl_auth_rule` VALUES ('8', 'Admin/group_del', '删除角色', '1', '1', '', '0', '1480577265', '1');
INSERT INTO `lxl_auth_rule` VALUES ('9', 'Admin/rulelist', '规则管理', '1', '1', '', '0', '1480577282', '1');
INSERT INTO `lxl_auth_rule` VALUES ('10', 'Admin/ruleadd', '添加规则', '1', '1', '', '0', '1480577303', '1');
INSERT INTO `lxl_auth_rule` VALUES ('11', 'Admin/rule_edit', '编辑规则', '1', '1', '', '0', '1480577319', '1');
INSERT INTO `lxl_auth_rule` VALUES ('12', 'Admin/rule_del', '删除规则', '1', '1', '', '0', '1480577337', '1');
INSERT INTO `lxl_auth_rule` VALUES ('13', 'Article/add', '添加文章', '1', '1', '', '0', '1483603331', '2');
INSERT INTO `lxl_auth_rule` VALUES ('14', 'Article/edit', '修改文章', '1', '1', '', '0', '0', '2');

-- ----------------------------
-- Table structure for `lxl_auth_rule_class`
-- ----------------------------
DROP TABLE IF EXISTS `lxl_auth_rule_class`;
CREATE TABLE `lxl_auth_rule_class` (
  `classid` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `classname` varchar(50) DEFAULT NULL COMMENT '分类名称',
  `order` smallint(5) unsigned DEFAULT '0',
  `addtime` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`classid`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='规则分类';

-- ----------------------------
-- Records of lxl_auth_rule_class
-- ----------------------------
INSERT INTO `lxl_auth_rule_class` VALUES ('1', '管理员设置', '0', '1480577076');
INSERT INTO `lxl_auth_rule_class` VALUES ('2', '文章管理', '0', '1483602881');
INSERT INTO `lxl_auth_rule_class` VALUES ('3', '统计', '0', '1483684720');
