<?php
global $_W,$_GPC;
$uniacid = $_W['uniacid'];
load()->func('tpl');

# 显示快递名字
include $this->template('sweep');