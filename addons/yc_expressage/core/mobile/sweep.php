<?php
global $_W,$_GPC;
$uniacid = $_W['uniacid'];
$uid = $_W['uid'];
load()->func('tpl');
#检测是否是微信登陆
checkMobile();

# 显示快递名字
include $this->template('sweep');