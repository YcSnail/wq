<?php
// +----------------------------------------------------------------------
// | WSHOTO [ 技术主导，服务至上，提供微信端解决方案 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2020 http://www.wshoto.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: yc <yc@yuanxu.top>
// +----------------------------------------------------------------------

# 公告管理
global $_W,$_GPC;

$do = $_GPC['do'];

load()->func('tpl');
#表名
$tbName = 'yc_shop_notice';
$op = empty($_GPC['op']) ?'display':$_GPC['op'] ;




include $this->template('shop/notice');